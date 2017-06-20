<?php
	$content = '';
	
	if (rex_post('config-submit', 'boolean')) {
		$this->setConfig(rex_post('config', [
			['debugmode', 'bool'],
			['minifyhtml', 'bool'],
			['pathcss', 'string'],
			['pathjs', 'string'],
			['templates', 'array[int]'],
			['tinifyactive', 'bool'],
			['tinifykey', 'string'],
			['php_optim_png', 'string'],
			['php_optim_gif', 'string'],
			['php_optim_jpeg', 'string'],
			['php_optim_png_path', 'string'],
			['php_optim_gif_path', 'string'],
			['php_optim_jpeg_path', 'string'],
		]));

		$content .= rex_view::info($this->i18n('config_saved'));
	}

	$content .= '<div class="rex-form">';
	$content .= '  <form action="'.rex_url::currentBackendPage().'" method="post">';
	$content .= '    <fieldset>';
	
	$formElements = [];
	
	//Start - minify_html
		$n = [];
		$n['label'] = '<label for="minify-config-minifyhtml">'.$this->i18n('config_minifyhtml').'</label>';
		$n['field'] = '<input type="checkbox" id="minify-config-minifyhtml" name="config[minifyhtml]" value="1" '.($this->getConfig('minifyhtml') ? ' checked="checked"' : '').'>';
		$formElements[] = $n;
	//End - minify_html

	//Start - path_css
		$n = [];
		$n['label'] = '<label for="minify-config-pathcss">'.$this->i18n('config_pathcss').'</label>';
		$n['field'] = '<input type="text" id="minify-config-pathcss" name="config[pathcss]" value="'.$this->getConfig('pathcss').'"/>';
		$formElements[] = $n;
	//End - path_css
	
	//Start - path_js
		$n = [];
		$n['label'] = '<label for="minify-config-pathjs">'.$this->i18n('config_pathjs').'</label>';
		$n['field'] = '<input type="text" id="minify-config-pathjs" name="config[pathjs]" value="'.$this->getConfig('pathjs').'"/>';
		$formElements[] = $n;
	//End - path_js
	
	//Start - templates
		$n = [];
		$n['label'] = '<label for="minify-config-templates">' . $this->i18n('config_templates') . '</label>';
		$select = new rex_select();
		$select->setId('minify-config-templates');
		$select->setMultiple();
		$select->setSize(10);
		$select->setAttribute('class', 'form-control');
		$select->setName('config[templates][]');
		$select->addSqlOptions('SELECT `name`, `id` FROM `' . rex::getTablePrefix() . 'template` ORDER BY `name` ASC');
		$select->setSelected($this->getConfig('templates'));
		$n['field'] = $select->get();
		$formElements[] = $n;
	//End - templates
	
	//Start - tinify_active
		$n = [];
		$n['label'] = '<label for="minify-config-tinifyactive">'.$this->i18n('config_tinifyactive').'</label>';
		$n['field'] = '<input type="checkbox" id="minify-config-tinifyactive" name="config[tinifyactive]" value="1" '.($this->getConfig('tinifyactive') ? ' checked="checked"' : '').'>';
		$formElements[] = $n;
	//End - tinify_active
	
	//Start - tinify_key
		$n = [];
		$n['label'] = '<label for="minify-config-tinifykey">'.$this->i18n('config_tinifykey').'</label>';
		$n['field'] = '<input type="text" id="minify-config-tinifykey" name="config[tinifykey]" value="'.$this->getConfig('tinifykey').'"/>';
		$formElements[] = $n;
	//End - tinify_key

	//Start - php_optim_gif
		$n = [];
		$options = ['<option value="">-- '. $this->i18n('config_no_php_optim_processor') .' --</option>'];
		$files = glob(__DIR__ .'/../vendor/PHPImageOptim/Tools/Gif/*.php');

		foreach ($files as $file) {
			$name = basename($file);
			$options[] = '<option value="'. $name .'" '. ($this->getConfig('php_optim_gif') == $name ? 'selected="selected"' : '') .'>'. substr($name, 0, -4) .'</option>';
		}
		$n['label'] = '<label>'.$this->i18n('config_php_optim_gif').'</label>';
		$n['field'] = '<select name="config[php_optim_gif]">'. implode('', $options) .'</select>';
		$formElements[] = $n;

		$n = [];
		$n['label'] = '<label for="minify-config-php_optim_gif_path">'. strtr($this->i18n('config_php_optim_path'), ['%s' => strtolower(substr($name, 0, -4))]) .'</label>';
		$n['field'] = '<input type="text" id="minify-config-php_optim_gif_path" name="config[php_optim_gif_path]" value="'.$this->getConfig('php_optim_gif_path').'"/>';
		$formElements[] = $n;
	//End - php_optim_gif

	//Start - php_optim_png
		$n = [];
		$options = ['<option value="">-- '. $this->i18n('config_no_php_optim_processor') .' --</option>'];
		$files = glob(__DIR__ .'/../vendor/PHPImageOptim/Tools/Png/*.php');

		foreach ($files as $file) {
			$name = basename($file);
			$options[] = '<option value="'. $name .'" '. ($this->getConfig('php_optim_png') == $name ? 'selected="selected"' : '') .'>'. substr($name, 0, -4) .'</option>';
		}
		$n['label'] = '<label>'.$this->i18n('config_php_optim_png').'</label>';
		$n['field'] = '<select name="config[php_optim_png]">'. implode('', $options) .'</select>';
		$formElements[] = $n;

		$n = [];
		$n['label'] = '<label for="minify-config-php_optim_png_path">'. strtr($this->i18n('config_php_optim_path'), ['%s' => strtolower(substr($name, 0, -4))]) .'</label>';
		$n['field'] = '<input type="text" id="minify-config-php_optim_png_path" name="config[php_optim_png_path]" value="'.$this->getConfig('php_optim_png_path').'"/>';
		$formElements[] = $n;
	//End - php_optim_png

	//Start - php_optim_jpeg
		$n = [];
		$options = ['<option value="">-- '. $this->i18n('config_no_php_optim_processor') .' --</option>'];
		$files = glob(__DIR__ .'/../vendor/PHPImageOptim/Tools/Jpeg/*.php');

		foreach ($files as $file) {
			$name = basename($file);
			$options[] = '<option value="'. $name .'" '. ($this->getConfig('php_optim_jpeg') == $name ? 'selected="selected"' : '') .'>'. substr($name, 0, -4) .'</option>';
		}
		$n['label'] = '<label>'.$this->i18n('config_php_optim_jpeg').'</label>';
		$n['field'] = '<select name="config[php_optim_jpeg]">'. implode('', $options) .'</select>';
		$formElements[] = $n;

		$n = [];
		$n['label'] = '<label for="minify-config-php_optim_jpeg_path">'. strtr($this->i18n('config_php_optim_path'), ['%s' => strtolower(substr($name, 0, -4))]) .'</label>';
		$n['field'] = '<input type="text" id="minify-config-php_optim_jpeg_path" name="config[php_optim_jpeg_path]" value="'.$this->getConfig('php_optim_jpeg_path').'"/>';
		$formElements[] = $n;
	//End - php_optim_jpeg
	
	$fragment = new rex_fragment();
	$fragment->setVar('elements', $formElements, false);
	$content .= $fragment->parse('core/form/form.php');
	
	$content .= '    </fieldset>';
	
	$content .= '    <fieldset class="rex-form-action">';
	
	$formElements = [];
	
	$n = [];
	$n['field'] = '<input type="submit" name="config-submit" value="'.$this->i18n('config_action_save').'" '.rex::getAccesskey($this->i18n('config_action_save'), 'save').'>';
	$formElements[] = $n;
	
	$fragment = new rex_fragment();
	$fragment->setVar('elements', $formElements, false);
	$content .= $fragment->parse('core/form/submit.php');
	
	$content .= '    </fieldset>';
	$content .= '  </form>';
	$content .= '</div>';
	
	$fragment = new rex_fragment();
	$fragment->setVar('class', 'edit');
	$fragment->setVar('title', $this->i18n('config'));
	$fragment->setVar('body', $content, false);
	echo $fragment->parse('core/page/section.php');
?>