<?php

class minify
{
    private $files = [];
    private $addon;

    public function __construct()
    {
        $this->addon = rex_addon::get(__CLASS__);
    }

    public function addFile($file, $set = 'default')
    {
        $this->files[$set][] = $file;
    }

    public static function isSCSS($file)
    {
        $pos = strrpos($file, '.');
        if (false !== $pos) {
            return 'scss' == substr($file, $pos + 1);
        }

        return false;
    }

    public static function compileFile($file, $type = 'scss')
    {
        $compiledFilename = rex_path::addonAssets(__CLASS__, 'cache' . '/compiled.scss.' . str_replace('.scss', '.css', basename($file)));

        $compiler = new rex_scss_compiler();
        $compiler->setScssFile(self::absolutePath($file));
        $compiler->setCssFile($compiledFilename);
        $compiler->compile();

        return $compiledFilename;
    }

    public static function absolutePath($file)
    {
        return rex_path::base($file);
    }

    public static function relativePath($file)
    {
        return substr($file, strlen(rex_path::base('')) - 1);
    }

    public function minify($type, $set = 'default', $output = 'file')
    {
        if (!in_array($type, ['css', 'js'])) {
            die('Minifyerror: Unknown type "' . $type . '"');
        }

        $minify   = false;
        $oldCache = [];
        $newCache = [];

        if (file_exists(rex_path::addonCache(__CLASS__, $type . '_' . rex_string::normalize($set) . '.json'))) {
            $string   = rex_file::get(rex_path::addonCache(__CLASS__, $type . '_' . rex_string::normalize($set) . '.json',''));
            $oldCache = json_decode($string, true);
        }

        if (!empty($this->files[$set])) {
            foreach ($this->files[$set] as $file) {
                if (!file_exists(trim(rex_path::base(substr($file, 1))))) {
                    die('Minifyerror: File "' . $file . '" does not exists');
                }

                //Start - get timestamp of the file
                $newCache[$file] = filemtime(trim(rex_path::base(substr($file, 1))));
                //End - get timestamp of the file

                if (empty($oldCache[$file])) {
                    $minify = true;
                } else {
                    if ($newCache[$file] > $oldCache[$file]) {
                        $minify = true;
                    }
                }
            }

            //Start - save path into cachefile
            if (!$minify) {
                $path = $oldCache['path'];
            }
            //End - save path into cachefile

            if ($minify) {
                switch ($type) {
                    case 'css':
                    $path     = rex_path::base(substr($this->addon->getConfig('pathcss'), 1) . '/bundled.' . rex_string::normalize($set) . '.' . $type);
                    $minifier = new MatthiasMullie\Minify\CSS();

                    break;
                    case 'js':
                    $path     = rex_path::base(substr($this->addon->getConfig('pathjs'), 1) . '/bundled.' . rex_string::normalize($set) . '.' . $type);
                    $minifier = new MatthiasMullie\Minify\JS();

                    break;
                }

                $newCache['path'] = $path;

                if (!rex_file::put(rex_path::addonCache(__CLASS__, $type . '_' . rex_string::normalize($set) . '.json'), json_encode($newCache))) {
                    echo 'Cachefile für ' . $type . ' konnte nicht geschrieben werden!';
                }

                foreach ($this->files[$set] as $file) {
                    $file = trim($file);

                    if (self::isSCSS($file)) {
                        $compiledFilename = self::compileFile($file, 'scss');
                        $minifier->add($compiledFilename);
                    } else {
                        $minifier->add(rex_path::base(substr($file, 1)));
                    }
                }

                $minifier->minify($path);
            }

            switch ($output) {
                case 'file':
                return self::relativePath($path);

                break;
                case 'inline':
                return rex_file::get($path);

                break;
            }
        }

        return false;
    }

    public static function clearCacheFiles()
    {
        $class = new self();
        $addon = $class->addon;

        $table = rex::getTable('minify_sets');
        $sql   = rex_sql::factory();
        $sql->setDebug(false);
        $sql->setTable($table);
        $sets = $sql->select('`name`, `type`')->getArray();

        //Start - delete minify files
        foreach ($sets as $set) {
            self::deleteSetFile($set['name'], $set['type'], 'clear_cache');
        }

        echo rex_view::success($addon->i18n('minify_sets_function_clear_cache_success'));
    }

    public static function deleteSetFile(string $set_name = '', string $set_type = '', string $func = '')
    {
        if (!empty($set_name) && !empty($set_type)) {
            $class = new self();
            $addon = $class->addon;

            $table = rex::getTable('minify_sets');
            $sql   = rex_sql::factory();
            $sql->setDebug(false);
            $sql->setTable($table);
            $sql->setWhere([
                'name' => $set_name,
                'type' => $set_type,
            ]);
            $set = $sql->select('`name`, `type`');

            $set_name = $sql->getValue('name');
            $set_type = $sql->getValue('type');

            if ('css' == $set_type) {
                $path = substr(rex_path::base(), 0, -1) . $addon->getConfig('pathcss') . '/';
            } elseif ('js' == $set_type) {
                $path = substr(rex_path::base(), 0, -1) . $addon->getConfig('pathjs') . '/';
            }

            $file = $path . 'bundled.' . $set_name . '.' . $set_type;
            if (file_exists($file)) {
                unlink($file);
                if (empty($func)) {
                    echo rex_view::success($addon->i18n('minify_sets_function_clear_cache_file_success'));
                }
            } else {
                if (empty($func)) {
                    echo rex_view::error($addon->i18n('minify_sets_function_clear_cache_file_error'));
                }
            }

            //Start - delete all addon cache files
            $path = $addon->getCachePath('');
            foreach (glob($path . '*.json') as $filename) {
                unlink($filename);
            }
            // End - delete all cache files
        }
    }
}
