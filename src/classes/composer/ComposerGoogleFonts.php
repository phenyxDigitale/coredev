<?php

class ComposerGoogleFonts extends Composer {

	public function render($settings, $value) {

		$fields = [];
		$values = [];
		$set = isset($settings['settings'], $settings['settings']['fields']) ? $settings['settings']['fields'] : [];
		extract($this->_vc_google_fonts_parse_attributes($set, $value));
		ob_start();
		
        $data = $context->smarty->createTemplate(_EPH_COMPOSER_DIR_ .  'goofle_fonts/template.tpl');
        $data->assign(
				[
					'values' => $values,
                    'fields' => $fields,
                    'settings' => $settings,
                    'value'   => $value,
                    'fonts' => $this->_vc_google_fonts_get_fonts()
				]
			);
        $data->fetch();

		return ob_get_clean();
	}

	public function _vc_google_fonts_get_fonts() {

		if (file_exists(_EPH_CONFIG_DIR_ . 'helpers/google_fonts.json')) {
			return json_decode(file_get_contents(_EPH_CONFIG_DIR_ . 'helpers/google_fonts.json'));
		}

		return null;
	}

	public function _vc_google_fonts_parse_attributes($attr, $value) {

		$fields = [];

		if (is_array($attr) && !empty($attr)) {

			foreach ($attr as $key => $val) {

				if (is_numeric($key)) {
					$fields[$val] = "";
				} else {
					$fields[$key] = $val;
				}

			}

		}

		$values = parse_multi_attribute($value, [
			'font_family'             => isset($fields['font_family']) ? $fields['font_family'] : '',
			'font_style'              => isset($fields['font_style']) ? $fields['font_style'] : '',
			'font_family_description' => isset($fields['font_family_description']) ? $fields['font_family_description'] : '',
			'font_style_description'  => isset($fields['font_style_description']) ? $fields['font_style_description'] : '',
		]);

		return ['fields' => $fields, 'values' => $values];
	}

}
