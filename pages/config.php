<?php

$content = '';

if (rex_post('config-submit', 'boolean')) {
    $this->setConfig(rex_post('config', [
        ['debugmode', 'bool'],
        ['minifyhtml', 'bool'],
        ['pathcss', 'string'],
        ['pathjs', 'string'],
        ['templates', 'array[int]'],
    ]));

    $content .= rex_view::info($this->i18n('config_saved'));
}

$content .= '<div class="rex-form">';
$content .= '  <form action="' . rex_url::currentBackendPage() . '" method="post">';
$content .= '    <fieldset>';

$formElements = [];

//Start - minify_html
$n              = [];
$n['label']     = '<label for="minify-config-minifyhtml">' . $this->i18n('config_minifyhtml') . '</label>';
$n['field']     = '<input type="checkbox" id="minify-config-minifyhtml" name="config[minifyhtml]" value="1" ' . ($this->getConfig('minifyhtml') ? ' checked="checked"' : '') . '>';
$formElements[] = $n;
//End - minify_html

//Start - path_css
$n              = [];
$n['label']     = '<label for="minify-config-pathcss">' . $this->i18n('config_pathcss') . '</label>';
$n['field']     = '<input class="form-control" type="text" id="minify-config-pathcss" name="config[pathcss]" value="' . $this->getConfig('pathcss') . '" placeholder="' . $this->i18n('config_pathcss_placeholder') . '"/>';
$n['note']      = rex_i18n::rawMsg('minify_config_hint');
$formElements[] = $n;
//End - path_css

//Start - path_js
$n              = [];
$n['label']     = '<label for="minify-config-pathjs">' . $this->i18n('config_pathjs') . '</label>';
$n['field']     = '<input class="form-control" type="text" id="minify-config-pathjs" name="config[pathjs]" value="' . $this->getConfig('pathjs') . '" placeholder="' . $this->i18n('config_pathjs_placeholder') . '"/>';
$n['note']      = rex_i18n::rawMsg('minify_config_hint');
$formElements[] = $n;
//End - path_js

//Start - templates
$n          = [];
$n['label'] = '<label for="minify-config-templates">' . $this->i18n('config_templates') . '</label>';
$select     = new rex_select();
$select->setId('minify-config-templates');
$select->setMultiple();
$select->setAttribute('class', 'form-control selectpicker');
$select->setAttribute('data-live-search', 'true');
$select->setName('config[templates][]');
$select->addSqlOptions('SELECT `name`, `id` FROM `' . rex::getTable('template') . '` ORDER BY `name` ASC');
$select->setSelected($this->getConfig('templates'));
$n['field']     = $select->get();
$formElements[] = $n;
//End - templates

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/form.php');

$content .= '    </fieldset>';

$content .= '    <fieldset class="rex-form-action">';

$formElements = [];

$n              = [];
$n['field']     = '<input class="btn btn-save" type="submit" name="config-submit" value="' . $this->i18n('config_action_save') . '" ' . rex::getAccesskey($this->i18n('config_action_save'), 'save') . '>';
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
