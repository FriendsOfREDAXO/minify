<?php
	class rex_effect_tinify extends rex_effect_abstract {
		public function execute() {
			if (rex_addon::get('minify')->getConfig('tinifyactive')) {
				$key = rex_addon::get('minify')->getConfig('tinifykey');
				if (!$key) {
					return;
				}
				
				\Tinify\Tinify::setKey($key);
				
				$this->media->asImage();
				
				$format = $this->media->getFormat();

				//Start - get imageSource
				ob_start();
				if ($format == 'jpg' || $format == 'jpeg') {
					imagejpeg($this->media->getImage(), null, rex_config::get('media_manager', 'jpg_quality', 80));
				}
				elseif ($format == 'png') {
					imagepng($this->media->getImage());
				}
				else {
					return;
				}
				ob_end_clean();
				//End - get imageSource
				
				$source = \Tinify\fromBuffer($src);
				
				$buffer = $source->toBuffer();
				
				$img = imagecreatefromstring($buffer);
				$this->media->setImage($img);
			}
		}
	}
?>
