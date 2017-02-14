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
				
				//Start - get imageSource
					ob_start();
					if ($this->image['format'] == 'jpg' || $this->image['format'] == 'jpeg') {
						$this->image['quality'] = rex_config::get('media_manager', 'jpg_quality', 80);
						imagejpeg($this->image['src'], null, $this->image['quality']);
					} elseif ($this->image['format'] == 'png') {
						imagepng($this->image['src']);
					} else {
						return;
					}
					$src = ob_get_contents();
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
