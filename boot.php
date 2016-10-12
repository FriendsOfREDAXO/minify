<?php
	if (!rex::isBackend()) {
		rex_extension::register('OUTPUT_FILTER', function(rex_extension_point $ep) {
			$content = $ep->getSubject();
			preg_match_all("/REX_MINIFY\[type=(.*)\ set=(.*)\]/", $content, $matches, PREG_SET_ORDER);
			
			foreach ($matches as $match) {
				//Start - get set by name and type
					$sql = rex_sql::factory();
					$sets = $sql->getArray('SELECT `assets`, `attributes`, `output` FROM `'.rex::getTablePrefix().'minify_sets` WHERE type = ? AND name = ?', [$match[1], $match[2]]);
					unset($sql);
				//End - get set by name and type
				
				if (!empty($sets)) {
					$assets = explode(PHP_EOL, $sets[0]['assets']);
					
					if ($this->getConfig('debugmode')) {
						$assetsContent = '';
						foreach($assets as $asset) {
							switch ($match[1]) {
								case 'css':
									if (minify::isSCSS($asset)) {
										$asset = minify::compileFile($asset, 'scss');
									} else {
										$asset = rex_path::base(substr($asset,1));
									}
									
									switch ($sets[0]['output']) {
										case 'inline':
											$assetsContent = '<style '.((!empty($sets[0]['attributes'])) ? implode(' ', explode(PHP_EOL, $sets[0]['attributes'])) : '').'>'.rex_file::get($asset).'</style>';
										break;
										default:
											$assetsContent .= '<link rel="stylesheet" href="'.minify::relativePath($asset).'" '.((!empty($sets[0]['attributes'])) ? implode(' ', explode(PHP_EOL, $sets[0]['attributes'])) : '').'>';
										break;
									}
								break;
								case 'js':
									$asset = rex_path::base(substr($asset,1));
									
									switch ($sets[0]['output']) {
										case 'inline':
											$assetsContent .= '<script '.((!empty($sets[0]['attributes'])) ? implode(' ', explode(PHP_EOL, $sets[0]['attributes'])) : '').'>'.rex_file::get($asset).'</script>';
										break;
										default:
											$assetsContent .= '<script src="'.minify::relativePath($asset).'" '.((!empty($sets[0]['attributes'])) ? implode(' ', explode(PHP_EOL, $sets[0]['attributes'])) : '').'></script>';
										break;
									}
								break;
							}
						}
						
						$content = str_replace($match[0], $assetsContent, $content);
					} else {
						$minify = new minify();
						foreach($assets as $asset) {
							$minify->addFile($asset, $match[2]);
						}
						
						$data = $minify->minify($match[1], $match[2], $sets[0]['output']);
						
						switch ($match[1]) {
							case 'css':
								switch ($sets[0]['output']) {
									case 'inline':
										$content = str_replace($match[0], '<style '.((!empty($sets[0]['attributes'])) ? implode(' ', explode(PHP_EOL, $sets[0]['attributes'])) : '').'>'.$data.'</style>', $content);
									break;
									default:
										$content = str_replace($match[0], '<link rel="stylesheet" href="'.$data.'" '.((!empty($sets[0]['attributes'])) ? implode(' ', explode(PHP_EOL, $sets[0]['attributes'])) : '').'>', $content);
									break;
								}
							break;
							case 'js':
								switch ($sets[0]['output']) {
									case 'inline':
										$content = str_replace($match[0], '<script '.((!empty($sets[0]['attributes'])) ? implode(' ', explode(PHP_EOL, $sets[0]['attributes'])) : '').'>'.$data.'</script>', $content);
									break;
									default:
										$content = str_replace($match[0], '<script src="'.$data.'" '.((!empty($sets[0]['attributes'])) ? implode(' ', explode(PHP_EOL, $sets[0]['attributes'])) : '').'></script>', $content);
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
					$content = preg_replace(['/<!--(.*)-->/Uis',"/[[:blank:]]+/"], ['',' '], str_replace(["\n","\r","\t"], '', $content));
				}
			//End - minify html
			
			$ep->setSubject($content);
		});
	}
?>