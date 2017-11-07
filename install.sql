CREATE TABLE IF NOT EXISTS `%TABLE_PREFIX%minify_sets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL DEFAULT '',
  `type` varchar(30) NOT NULL DEFAULT '',
  `minimize` enum('no','yes') NOT NULL DEFAULT 'no',
  `ignore_browsercache` enum('no','yes') NOT NULL DEFAULT 'no',
  `attributes` text NOT NULL DEFAULT '',
  `output` varchar(30) NOT NULL DEFAULT '',
  `assets` text NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;