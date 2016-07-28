<?php
	class minify {
		private $files = [];
		
		public function __construct() {
			require_once(rex_path::addon(__CLASS__, 'vendor/minify/src/CSS.php'));
			require_once(rex_path::addon(__CLASS__, 'vendor/minify/src/JS.php'));
		}
		
		public function addFile($file, $set = 'default') {
			$this->files[$set][] = $file;
		}
		
		public function minify($type, $set = 'default') {
			if (!in_array($type, ['css','js'])) {
				return false;
			}
			
			$minify = false;
			$oldCache = [];
			$newCache = [];
			
			if (file_exists(rex_path::addonAssets(__CLASS__, 'cache'.'/'.$type.'_'.$set.'.json'))) {
				$string = file_get_contents(rex_path::addonAssets(__CLASS__, 'cache'.'/'.$type.'_'.$set.'.json'));
				$oldCache = json_decode($string, true);
			}
			
			if (!empty($this->files[$set])) {
				foreach ($this->files[$set] as $file) {
					//Start - get timestamp of the file
						$newCache[$file] = filemtime(trim(rex_path::base(substr($file,1))));
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
				//Ebd - save path into cachefile
				
				if ($minify) {
					$path = rex_path::addonAssets(__CLASS__, 'cache'.'/'.md5($set.'_'.implode(',',$newCache).'_'.time()).'.'.$type);
					$newCache['path'] = $path;
					
					switch($type) {
						case 'css':
							$minifier = new MatthiasMullie\Minify\CSS();
						break;
						case 'js':
							$minifier = new MatthiasMullie\Minify\JS();
						break;
					}
					
					if (!rex_file::put(rex_path::addonAssets(__CLASS__, 'cache'.'/'.$type.'_'.$set.'.json'), json_encode($newCache))) {
						echo 'Cachefile für '.$type.' konnte nicht geschrieben werden!';
					}
					
					foreach ($this->files[$set] as $file) {
						$minifier->add(trim(rex_path::base(substr($file,1))));
					}
					
					$minifier->minify($path);
				}
				
				return substr($path,strlen(rex_path::base(''))-1);
			}
			
			return false;
		}
	}
?>