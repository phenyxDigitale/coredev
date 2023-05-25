<?php

class ComposerShortCode_vc_accordion extends ComposerShortCode {

	protected $controls_css_settings = 'out-tc vc_controls-content-widget';
	public function __construct($settings) {

		parent::__construct($settings);
	}

	public function contentAdmin($atts, $content = null) {

		$width = $custom_markup = '';
		$shortcode_attributes = ['width' => '1/1'];

		foreach ($this->settings['params'] as $param) {

			if ($param['param_name'] != 'content') {

				if (isset($param['value']) && is_string($param['value'])) {
					$shortcode_attributes[$param['param_name']] = $param['value'];
				} else if (isset($param['value'])) {
					$shortcode_attributes[$param['param_name']] = $param['value'];
				}

			} else
			if ($param['param_name'] == 'content' && $content == NULL) {
				$content = $param['value'];
			}

		}

		
		$atts = Composer::shortcode_atts($shortcode_attributes, $atts);
		extract($atts);
		$output = '';

		$elem = $this->getElementHolder($width);

		$inner = '';

		foreach ($this->settings['params'] as $param) {
			$param_value = '';
			$param_value = isset($atts[$param['param_name']]) ? $atts[$param['param_name']] : '';

			if (is_array($param_value)) {
				// Get first element from the array
				reset($param_value);
				$first_key = key($param_value);
				$param_value = $param_value[$first_key];
			}

			$inner .= $this->singleParamHtmlHolder($param, $param_value);
		}

		
		$tmp = '';
		

		if (isset($this->settings["custom_markup"]) && $this->settings["custom_markup"] != '') {

			if ($content != '') {
				$custom_markup = str_ireplace("%content%", $tmp . $content, $this->settings["custom_markup"]);
			} else
			if ($content == '' && isset($this->settings["default_content_in_template"]) && $this->settings["default_content_in_template"] != '') {
				$custom_markup = str_ireplace("%content%", $this->settings["default_content_in_template"], $this->settings["custom_markup"]);
			} else {
				$custom_markup = str_ireplace("%content%", '', $this->settings["custom_markup"]);
			}

			
			$inner .= Composer::do_shortcode($custom_markup);
		}

		$elem = str_ireplace('%wpb_element_content%', $inner, $elem);
		$output = $elem;

		return $output;
	}

}
