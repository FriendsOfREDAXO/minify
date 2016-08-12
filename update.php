<?php
	rex_sql_table::get(rex::getTable('minify_sets'))
	->ensureColumn(new rex_sql_column('media', 'varchar(250)'))
	->ensureColumn(new rex_sql_column('output', 'varchar(30)'))
	->alter();
?>