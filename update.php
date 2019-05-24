<?php
rex_sql_table::get(rex::getTable('minify_sets'))
  ->ensureColumn(new rex_sql_column('media', 'text'))
  ->ensureColumn(new rex_sql_column('attributes', 'text'))
  ->ensureColumn(new rex_sql_column('output', 'varchar(30)'))
  ->ensureColumn(new rex_sql_column('minimize', 'ENUM(\'no\',\'yes\')'))
  ->ensureColumn(new rex_sql_column('ignore_browsercache', 'ENUM(\'no\',\'yes\')'))
  ->alter();

$sql = rex_sql::factory();
$sql->setQuery('UPDATE `' . rex::getTablePrefix() . 'minify_sets` SET attributes = CONCAT("media=\"",media,"\"") WHERE `media` != ""');
unset($sql);

rex_sql_table::get(rex::getTable('minify_sets'))
  ->removeColumn('media')
  ->alter();

if (!$this->hasConfig('pathcss')) {
  $this->setConfig(['pathcss' => '/assets/addons/minify/cache']);
}

if (!$this->hasConfig('pathjs')) {
  $this->setConfig(['pathjs' => '/assets/addons/minify/cache']);
}
?>