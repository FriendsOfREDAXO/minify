<?php
	if (!rex::isBackend()) {
		rex_extension::register('OUTPUT_FILTER', function(rex_extension_point $ep) {
			$content = $ep->getSubject();
			preg_match_all("/REX_MINIFY\[type=(.*)\ set=(.*)\]/", $content, $matches, PREG_SET_ORDER);
			
			foreach ($matches as $match) {
				//Start - get set by name and type
					$sql = rex_sql::factory();
					$sets = $sql->getArray('SELECT `assets` FROM `'.rex::getTablePrefix().'minify_sets` WHERE type = ? AND name = ?', [$match[1], $match[2]]);
					unset($sql);
				//End - get set by name and type
				
				if (!empty($sets)) {
					$assets = explode(PHP_EOL, $sets[0]['assets']);
					
					$minify = new rex_minify();
					foreach($assets as $asset) {
						$minify->addFile($asset, $match[2]);
					}
					
					$path = $minify->minify($match[1], $match[2]);
					
					switch ($match[1]) {
						case 'css':
							$content = str_replace($match[0], '<link rel="stylesheet" href="'.$path.'">', $content);
						break;
						case 'js':
							$content = str_replace($match[0], '<script src="'.$path.'"></script>', $content);
						break;
					}
					
				} else {
					$content = str_replace($match[0], '', $content);
				}
			}
			
			$ep->setSubject($content);
		});
	}
?>