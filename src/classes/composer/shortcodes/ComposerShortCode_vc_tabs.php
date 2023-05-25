<?php
/**
 */
class ComposerShortCode_vc_tabs extends ComposerShortCode {

	static $filter_added = false;
	protected $controls_css_settings = 'out-tc vc_controls-content-widget';
	protected $controls_list = ['edit', 'clone', 'delete'];
	public function __construct($settings) {

		parent::__construct($settings);

		if (!self::$filter_added) {
			$this->addFilter('vc_inline_template_content', 'setCustomTabId');
			self::$filter_added = true;
		}

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
				//$content = $param['value'];
				$content = $param['value'];
			}

		}

		
		$atts = Composer::shortcode_atts($shortcode_attributes, $atts);
		extract($atts);
		

		preg_match_all('/vc_tab title="([^\"]+)"(\stab_id\=\"([^\"]+)\"){0,1}/i', $content, $matches, PREG_OFFSET_CAPTURE);
		
		$output = '';
		$tab_titles = [];

		if (isset($matches[0])) {
			$tab_titles = $matches[0];
		}

		$tmp = '';

		if (count($tab_titles)) {
			$tmp .= '<ul class="clearfix tabs_controls">';

			foreach ($tab_titles as $tab) {
				preg_match('/title="([^\"]+)"(\stab_id\=\"([^\"]+)\"){0,1}/i', $tab[0], $tab_matches, PREG_OFFSET_CAPTURE);

				if (isset($tab_matches[1][0])) {
					$tmp .= '<li><a href="#tab-' . (isset($tab_matches[3][0]) ? $tab_matches[3][0] : $tab_matches[1][0]) . '">' . $tab_matches[1][0] . '</a></li>';

				}

			}

			$tmp .= '</ul>' . "\n";
		} else {
			$output .= Composer::do_shortcode($content);
		}

		
		$elem = $this->getElementHolder($width);

		$iner = '';

		foreach ($this->settings['params'] as $param) {
			$custom_markup = '';
			$param_value = isset($atts[$param['param_name']]) ? $atts[$param['param_name']] : '';

			if (is_array($param_value)) {
				// Get first element from the array
				reset($param_value);
				$first_key = key($param_value);
				$param_value = $param_value[$first_key];
			}

			$iner .= $this->singleParamHtmlHolder($param, $param_value);
		}

		

		if (isset($this->settings["custom_markup"]) && $this->settings["custom_markup"] != '') {

			if ($content != '') {
				$custom_markup = str_ireplace("%content%", $tmp . $content, $this->settings["custom_markup"]);
			} else
			if ($content == '' && isset($this->settings["default_content_in_template"]) && $this->settings["default_content_in_template"] != '') {
				$custom_markup = str_ireplace("%content%", $this->settings["default_content_in_template"], $this->settings["custom_markup"]);
			} else {
				$custom_markup = str_ireplace("%content%", '', $this->settings["custom_markup"]);
			}

			
			$iner .= Composer::do_shortcode($custom_markup);
		}

		$elem = str_ireplace('%wpb_element_content%', $iner, $elem);
		$output = $elem;

		return $output;
	}

	public function getTabTemplate() {

		return '<div class="wpb_template">' . Composer::do_shortcode('[vc_tab title="Tab" tab_id=""][/vc_tab]') . '</div>';
	}

	public function setCustomTabId($content) {

		return preg_replace('/tab\_id\=\"([^\"]+)\"/', 'tab_id="$1-' . time() . '"', $content);
	}

}
