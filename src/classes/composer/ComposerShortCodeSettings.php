<?php

class ComposerShortCodeSettings extends ComposerShortCodeUniversalAdmin {

	public function content($atts, $content = null) {

		return $this->contentAdmin($atts, $content);
	}

	public function contentAdmin($atts, $content = null) {

        $file = fopen("testDhortCodeSeetingContentAdmin.txt","w");
		$this->loadDefaultParams();
		$output = $el_position = '';
		$groups_content = [];
		$vc_manager = new Composer();
        fwrite($file,print_r($this->settings, true));
		if (isset($this->settings['params'])) {
			$shortcode_attributes = [];

			foreach ($this->settings['params'] as $param) {

				if ($param['param_name'] != 'content') {

					if (isset($param['std'])) {
						$shortcode_attributes[$param['param_name']] = $param['std'];
					} else {
						$shortcode_attributes[$param['param_name']] = isset($param['value']) ? $param['value'] : null;
					}

				} else
				if ($param['param_name'] == 'content' && $content === null) {
					$content = isset($param['value']) ? $param['value'] : '';
				}

			}

			$attrs = Composer::admin_shortcode_atts(
				$shortcode_attributes
				, $atts);
			extract($attrs);

			$editor_css_classes = ['vc_row wpb_edit_form_elements'];
			$output .= '<div class="' . implode(' ', $editor_css_classes) . '" data-title="' . htmlspecialchars($vc_manager->l('Edit') . ' ' . $this->settings['name']) . '">';

			foreach ($this->settings['params'] as $param) {

				$param_value = isset($attrs[$param['param_name']]) ? $attrs[$param['param_name']] : '';

				if (is_array($param_value) && !empty($param_value) && isset($param['std'])) {
					$param_value = $param['std'];
				} else if (is_array($param_value) && !empty($param_value) && !empty($param['type']) && $param['type'] != 'checkbox') {

					reset($param_value);
					$first_key = key($param_value);
					$param_value = $param_value[$first_key];
				} else if (is_array($param_value)) {
					$param_value = '';
				}

				$group = isset($param['group']) && $param['group'] !== '' ? $param['group'] : '_general';

				if (!isset($groups_content[$group])) {
					$groups[] = $group;
					$groups_content[$group] = '';
				}

				$groups_content[$group] .= $this->singleParamEditHolder($param, $param_value);
			}

			if (sizeof($groups) > 1) {
				$output .= '<div class="vc_panel-tabs" id="vc_edit-form-tabs"><ul>';
				$key = 0;

				foreach ($groups as $g) {
					$output .= '<li><a href="#vc_edit-form-tab-' . $key++ . '">' . ($g === '_general' ? $vc_manager->l('General') : $g) . '</a></li>';
				}

				$output .= '</ul>';
				$key = 0;

				foreach ($groups as $g) {
					$output .= '<div id="vc_edit-form-tab-' . $key++ . '" class="vc_edit-form-tab">';
					$output .= $groups_content[$g];
					$output .= '</div>';
				}

				$output .= '</div>';
			} else if (!empty($groups_content['_general'])) {
				$output .= $groups_content['_general'];
			}

			$output .= '</div>'; //close wpb_edit_form_elements

			if (!ComposerShortcodeParams::isEnqueue()) {

				foreach (ComposerShortcodeParams::getScripts() as $script) {
					$output .= "\n\n" . '<doscript>JS::' . $script . '</doscript>';
				}

			}

		}

		return $output;
	}

	public function loadDefaultParams() {

		global $vc_params_list;
        $vc_params_list = ['textarea_html', 'colorpicker', 'loop', 'vc_link', 'options', 'sorted_list', 'css_editor', 'font_container', 'google_fonts', 'autocomplete', 'tab_id', 'href', 'el_id'];

		if (empty($vc_params_list)) {
			return false;
		}

		$script_url = _EPH_JS_DIR_ . 'composer/params/all.js';

		foreach ($vc_params_list as $param) {
			add_shortcode_param($param, 'vc_' . $param . '_form_field', $script_url);
		}

		load_column_offset_param();
	}

}
