<?php

class rex_effect_phpoptim extends rex_effect_abstract
{
    public function execute()
    {
        $this->media->asImage();

        $format      = $this->media->getFormat();
        $processor   = rex_addon::get('minify')->getConfig('php_optim_' . $format);
        $binary_path = rex_addon::get('minify')->getConfig('php_optim_' . $format . '_path');

        if (!strlen($processor) || !strlen($binary_path)) {
            return;
        }

        include_once __DIR__ . '/../vendor/PHPImageOptim/Tools/ToolsInterface.php';
        include_once __DIR__ . '/../vendor/PHPImageOptim/Tools/Common.php';
        include_once __DIR__ . '/../vendor/PHPImageOptim/Tools/' . ucfirst($format) . '/' . $processor;
        include_once __DIR__ . '/../vendor/PHPImageOptim/PHPImageOptim.php';

        $filepath = rex_path::cache('phpoptim.tmp');

        switch ($format) {
            case 'jpeg':
                imagejpeg($this->media->getImage(), $filepath, rex_config::get('media_manager', 'jpg_quality', 80));
                break;
            case 'png':
                imagepng($this->media->getImage(), $filepath, rex_config::get('media_manager', 'jpg_quality', 80));
                break;
            case 'gif':
                imagegif($this->media->getImage(), $filepath);
                break;
        }

        $class     = "\\PHPImageOptim\\Tools\\Jpeg\\" . substr($processor, 0, -4);
        $Processor = new $class();
        $Processor->setBinaryPath($binary_path);

        $Optim = new \PHPImageOptim\PHPImageOptim();
        $Optim->setImage($filepath);
        $Optim->chainCommand($Processor);
        $Optim->optimise();

        $this->media->setImage(imagecreatefromstring(file_get_contents($filepath)));
    }
}

?>
