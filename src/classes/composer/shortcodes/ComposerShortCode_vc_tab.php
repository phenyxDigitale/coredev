<?php
define('TAB_TITLE', ephenyx_manager()->l("Tab"));

class ComposerShortCode_vc_tab extends ComposerShortCode_vc_column {

	protected $controls_css_settings = 'tc vc_control-container';
	protected $controls_list = ['add', 'edit', 'clone', 'delete'];
	protected $predefined_atts = [
		'tab_id' => TAB_TITLE,
		'title'  => '',
	];
	protected $controls_template_file = 'editors/partials/backend_controls_tab.tpl';
	public function __construct($settings) {

		parent::__construct($settings);
	}

	public function customAdminBlockParams() {

		if (isset($this->atts['tab_id'])) {
			return ' id="tab-' . $this->atts['tab_id'] . '"';
		}

		return ' id="tab-"';
	}

	public function mainHtmlBlockParams($width, $i) {

		return 'data-element_type="' . $this->settings["base"] . '" class="wpb_' . $this->settings['base'] . ' wpb_sortable wpb_content_holder"' . $this->customAdminBlockParams();
	}

	public function containerHtmlBlockParams($width, $i) {

		return 'class="wpb_column_container vc_container_for_children"';
	}

	public function getColumnControls($controls, $extended_css = '') {

		return $this->getColumnControlsModular($extended_css);
		
	}

}

function tab_id_settings_field($settings, $value) {

	$dependency = generate_dependencies_attributes($settings);
	return '<div class="my_param_block">'
		. '<input name="' . $settings['param_name']
		. '" class="wpb_vc_param_value wpb-textinput '
		. $settings['param_name'] . ' ' . $settings['type'] . '_field" type="hidden" value="'
		. $value . '" ' . $dependency . ' />'
		. '<label>' . $value . '</label>'
		. '</div>';
	
}

add_shortcode_param('tab_id', 'tab_id_settings_field');