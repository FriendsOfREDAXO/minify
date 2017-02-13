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
				$source = \Tinify\fromFile($this->media->getMediapath());
				
				$buffer = $source->toBuffer();
				
				$img = imagecreatefromstring($buffer);
				$this->media->setImage($img);
			}
		}
	}
?>
