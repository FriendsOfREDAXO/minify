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
        $gdimage  = $this->media->getImage();

        switch ($format) {
            case 'jpeg':
                imagejpeg($gdimage, $filepath, rex_config::get('media_manager', 'jpg_quality', 80));
                break;
            case 'png':
                imagepng($gdimage, $filepath, 9);
                break;
            case 'gif':
                imagegif($gdimage, $filepath);
                break;
        }

        $class     = "\\PHPImageOptim\\Tools\\" . ucfirst($format) . "\\" . substr($processor, 0, -4);
        $Processor = new $class();
        $Processor->setBinaryPath($binary_path);

        try {
            $Optim = new \PHPImageOptim\PHPImageOptim();
            $Optim->setImage($filepath);
            $Optim->chainCommand($Processor);
            $Optim->optimise();

            $media_content = file_get_contents($filepath);
            $this->media->setMediaContent($media_content);
            $this->media->setImage(imagecreatefromstring($media_content));
        }
        catch (Exception $ex) {
        }
    }
}