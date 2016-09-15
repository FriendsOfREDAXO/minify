<?php
	rex_sql_table::get(rex::getTable('minify_sets'))
	->ensureColumn(new rex_sql_column('attributes', 'text'))
	->ensureColumn(new rex_sql_column('output', 'varchar(30)'))
	->alter();
	
	$sql = rex_sql::factory();
	$sql->setQuery('UPDATE `'.rex::getTablePrefix().'minify_sets` SET attributes = CONCAT("media=\"",media,"\"") WHERE `media` != ""');
	unset($sql);
	
	rex_sql_table::get(rex::getTable('minify_sets'))
	->removeColumn('media');
	->alter();
?>