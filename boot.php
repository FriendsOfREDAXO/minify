<?php

if (!rex::isBackend()) {
  rex_extension::register('CACHE_DELETED', ['minify', 'clearCacheFiles'], rex_extension::LATE);

  rex_extension::register('OUTPUT_FILTER', function (rex_extension_point $ep) {
    //Start - get php.ini settings
    $currentBacktrackLimit = ini_get('pcre.backtrack_limit');
    $currentRecursionLimit = ini_get('pcre.recursion_limit');
    //End - get php.ini settings

    //Start - set new php.ini-settings
    ini_set('pcre.backtrack_limit', 1000000);
    ini_set('pcre.recursion_limit', 1000000);
    //End - set new php.ini-settings

    $content = $ep->getSubject();

    $whitelistTemplates = rex_addon::get('minify')->getConfig('templates', []);
    if (null !== rex_article::getCurrent() && !in_array(rex_article::getCurrent()->getTemplateId(), $whitelistTemplates)) {
      preg_match_all("/REX_MINIFY\[type=(.*?)\ set=(.*?)\]/", $content, $matches, PREG_SET_ORDER);

      foreach ($matches as $match) {
        //Start - get set by name and type
        $sql  = rex_sql::factory();
        $sets = $sql->getArray('SELECT `minimize`, `ignore_browsercache`, `assets`, `attributes`, `output` FROM `' . rex::getTable('minify_sets') . '` WHERE `type` = ? AND `name` = ?', [$match[1], $match[2]]);
        unset($sql);
        //End - get set by name and type

        if (!empty($sets)) {
          $assets = explode(PHP_EOL, trim($sets[0]['assets']));

          if ('no' == $sets[0]['minimize']) {
            $assetsContent = '';
            foreach ($assets as $asset) {
              switch ($match[1]) {
                case 'css':
                  if (minify::isSCSS($asset)) {
                    $asset = minify::compileFile($asset, 'scss');
                  } else {
                    $asset = trim(rex_path::base(substr($asset, 1)));
                  }

                  switch ($sets[0]['output']) {
                    case 'inline':
                      $assetsContent = '<style ' . ((!empty($sets[0]['attributes'])) ? implode(' ', explode(PHP_EOL, $sets[0]['attributes'])) : '') . '>' . rex_file::get($asset) . '</style>';

                      break;
                    default:
                      // Parse attributes to check for custom rel attribute
                      $attributes = !empty($sets[0]['attributes']) ? explode(PHP_EOL, $sets[0]['attributes']) : [];
                      $hasRelAttribute = false;
                      $attributesStr = '';
                      
                      // Efficiently check for rel attribute and build attributes string
                      if (!empty($attributes)) {
                        foreach ($attributes as $attr) {
                          if (preg_match('/^rel\s*=/', $attr)) {
                            $hasRelAttribute = true;
                          }
                          $attributesStr .= ' ' . $attr;
                        }
                      }
                      
                      // Add rel="stylesheet" only if no custom rel is provided
                      $assetsContent .= '<link ' . ($hasRelAttribute ? '' : 'rel="stylesheet" ') . 
                                      'href="' . trim(minify::relativePath($asset)) . 
                                      (('yes' == $sets[0]['ignore_browsercache']) ? '?time=' . filemtime($asset) : '') . 
                                      '"' . $attributesStr . '>';
                      break;
                  }

                  break;
                case 'js':
                  $asset = trim(rex_path::base(substr($asset, 1)));

                  switch ($sets[0]['output']) {
                    case 'inline':
                      $assetsContent .= '<script ' . ((!empty($sets[0]['attributes'])) ? implode(' ', explode(PHP_EOL, $sets[0]['attributes'])) : '') . '>' . rex_file::get($asset) . '</script>';

                      break;
                    default:
                      $assetsContent .= '<script src="' . trim(minify::relativePath($asset)) . (('yes' == $sets[0]['ignore_browsercache']) ? '?time=' . filemtime($asset) : '') . '" ' . ((!empty($sets[0]['attributes'])) ? implode(' ', explode(PHP_EOL, $sets[0]['attributes'])) : '') . '></script>';

                      break;
                  }

                  break;
              }
            }

            $content = str_replace($match[0], $assetsContent, $content);
          } else {
            $minify = new minify();
            foreach ($assets as $asset) {
              $minify->addFile($asset, $match[2]);
            }

            $data = $minify->minify($match[1], $match[2], $sets[0]['output']);

            switch ($match[1]) {
              case 'css':
                switch ($sets[0]['output']) {
                  case 'inline':
                    $content = str_replace($match[0], '<style ' . ((!empty($sets[0]['attributes'])) ? implode(' ', explode(PHP_EOL, $sets[0]['attributes'])) : '') . '>' . $data . '</style>', $content);

                    break;
                  default:
                    // Parse attributes to check for custom rel attribute for minimized files
                    $attributes = !empty($sets[0]['attributes']) ? explode(PHP_EOL, $sets[0]['attributes']) : [];
                    $hasRelAttribute = false;
                    $attributesStr = '';
                    
                    // Efficiently check for rel attribute and build attributes string
                    if (!empty($attributes)) {
                      foreach ($attributes as $attr) {
                        if (preg_match('/^rel\s*=/', $attr)) {
                          $hasRelAttribute = true;
                        }
                        $attributesStr .= ' ' . $attr;
                      }
                    }
                    
                    // Add rel="stylesheet" only if no custom rel is provided
                    $content = str_replace($match[0], 
                                          '<link ' . ($hasRelAttribute ? '' : 'rel="stylesheet" ') . 
                                          'href="' . trim($data) . 
                                          (('yes' == $sets[0]['ignore_browsercache']) ? '?time=' . filemtime(ltrim($data, '/')) : '') . 
                                          '"' . $attributesStr . '>', 
                                          $content);
                    break;
                }

                break;
              case 'js':
                switch ($sets[0]['output']) {
                  case 'inline':
                    $content = str_replace($match[0], '<script ' . ((!empty($sets[0]['attributes'])) ? implode(' ', explode(PHP_EOL, $sets[0]['attributes'])) : '') . '>' . $data . '</script>', $content);

                    break;
                  default:
                    $content = str_replace($match[0], '<script src="' . trim($data) . (('yes' == $sets[0]['ignore_browsercache']) ? '?time=' . filemtime(ltrim($data, '/')) : '') . '" ' . ((!empty($sets[0]['attributes'])) ? implode(' ', explode(PHP_EOL, $sets[0]['attributes'])) : '') . '></script>', $content);

                    break;
                }

                break;
            }
          }
        } else {
          $content = str_replace($match[0], '', $content);
        }
      }

      //Start - minify html
      if ($this->getConfig('minifyhtml')) {
        if (rex_addon::get('search_it')->isAvailable()) {
          $pattern = '/<!--((?!search_it)[\s\S])*?-->/is';
        } else {
          $pattern = '/<!--[^\[](.|\s)*?[^\]]-->/is';
        }
        $content = preg_replace([$pattern, '/[[:blank:]]+/'], ['', ' '], str_replace(["\n", "\r", "\t"], '', $content));
      }
      //End - minify html
    }

    //Start - set old php.ini-settings
    ini_set('pcre.backtrack_limit', $currentBacktrackLimit);
    ini_set('pcre.recursion_limit', $currentRecursionLimit);
    //End - set old php.ini-settings

    $ep->setSubject($content);
  });
}
