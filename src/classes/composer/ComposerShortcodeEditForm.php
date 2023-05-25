<?php

class ComposerShortcodeEditForm {

	public $context;
    public static $actions = [];
    
	public function __construct() {
        global $smarty;
        $this->context = Context::getContext();
    }

	public function init() {

       
		Composer::$sds_action_hooks['wpb_show_edit_form'] = [ & $this, 'build'];

	}

	public function render() {
       
		$data = $this->context->smarty->createTemplate(_EPH_COMPOSER_DIR_  . 'editors/popups/panel_shortcode_edit_form.tpl');
		$data->assign(
			[
				'box'    => $this,
				'editor' => ephenyx_manager(),
			]
		);
		return $data->fetch();

	}

	public function build() {
        
        
        $vc_main = ephenyx_manager();

		$element = Tools::getValue('element');

		$shortCode = stripslashes(Tools::getValue('shortcode'));

		$params = Tools::getValue('params');

		ephenyx_composer()->removeShortCode($element);
		$settings = ComposerMap::getShortCode($element);
		$WPS = new ComposerShortCodeSettings($settings);
        $result = $WPS->contentAdmin($params);
		echo $result;
		die();
	}

	public static function changeEditFormFieldParams($param) {

		$css = $param['vc_single_param_edit_holder_class'];

		if (isset($param['edit_field_class'])) {
			$new_css = $param['edit_field_class'];
		} else {

			switch ($param['type']) {
			case 'attach_image':
			case 'attach_images':
			case 'textarea_html':
				$new_css = 'vc_col-sm-12 vc_column';
				break;
			default:
				$new_css = 'vc_col-sm-12 vc_column';
			}

		}

		array_unshift($css, $new_css);
		$param['vc_single_param_edit_holder_class'] = $css;
		return $param;
	}

	public function changeEditFormParams($css_classes) {

		$css = 'vc_row';
		array_unshift($css_classes, $css);
		return $css_classes;
	}

}
