<?php
	$content = '';
	
	if (rex_post('config-submit', 'boolean')) {
		$this->setConfig(rex_post('config', [
			['debugmode', 'bool'],
			['minifyhtml', 'bool'],
			['tinifykey', 'string'],
		]));
		
		$content .= rex_view::info($this->i18n('config_saved'));
	}

	$content .= '<div class="rex-form">';
	$content .= '  <form action="'.rex_url::currentBackendPage().'" method="post">';
	$content .= '    <fieldset>';
	
	$formElements = [];
	
	//Start - debugmode
		$n = [];
		$n['label'] = '<label for="minify-config-minifyhtml">'.$this->i18n('config_debugmode').'</label>';
		$n['field'] = '<input type="checkbox" id="minify-config-debugmode" name="config[debugmode]" value="1" '.($this->getConfig('debugmode') ? ' checked="checked"' : '').'>';
		$formElements[] = $n;
	//End - debugmode
	
	//Start - minify_html
		$n = [];
		$n['label'] = '<label for="minify-config-minifyhtml">'.$this->i18n('config_minifyhtml').'</label>';
		$n['field'] = '<input type="checkbox" id="minify-config-minifyhtml" name="config[minifyhtml]" value="1" '.($this->getConfig('minifyhtml') ? ' checked="checked"' : '').'>';
		$formElements[] = $n;
	//End - minify_html
	
	//Start - tinify_key
		$n = [];
		$n['label'] = '<label for="minify-config-tinifykey">'.$this->i18n('config_tinifykey').'</label>';
		$n['field'] = '<input type="text" id="minify-config-tinifykey" name="config[tinifykey]" value="'.$this->getConfig('tinifykey').'"/>';
		$formElements[] = $n;
	//End - tinify_key
	
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