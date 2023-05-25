<?php

class ComposerBackenEditor {

	protected $layout;

	public function addHooksSettings() {

		$actions = [
			'wpb_get_element_backend_html' => [&$this, 'elementBackendHtml'],
		];

		if ($action = Tools::getValue('action')) {

			if (isset($actions[$action])) {
				call_user_func($actions[$action]);
			}

		}

	}

	
	public function render() {

		$post_types = vc_editor_post_types();

		foreach ($post_types as $type) {
			add_meta_box('wpb_visual_composer', __('Visual Composer', "composer"), [&$this, 'renderEditor'], $type, 'normal', 'high');
		}

	}
    
    public function elementBackendHtml() {

		$jscomposer = Composer::getInstance();
		$data_element = Tools::getValue('data_element');

		if ($data_element == 'vc_column' && Tools::getValue('data_width') !== null) {
			$output = Composer::do_shortcode('[vc_column width="' . Tools::getValue('data_width') . '"]');
			echo $output;
		} else	if ($data_element == 'vc_row' || $data_element == 'vc_row_inner') {
			$output = Composer::do_shortcode('[' . $data_element . ']');
			echo $output;
		} else {
			$output = Composer::do_shortcode('[' . $data_element . ']');
			echo $output;
		}

		die();
	}


	

	

}
