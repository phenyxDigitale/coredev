<?php

class ComposerAutocomplete extends Composer {

	protected $settings;

	protected $value;

	protected $tag;

	public function __construct($settings, $value, $tag) {

		$this->tag = $tag;
		$this->settings = $settings;
		$this->value = $value;
	}

	public function render() {

		$output = '<div class="vc_autocomplete-field">';
		$selected_items = $data_names = '';

		if (isset($this->value) && strlen($this->value) > 0) {
			$values = explode('-', $this->value);

			foreach ($values as $key => $val) {
				$query = [
					'value' => trim($val),
					'type'  => $this->settings['settings']['vc_catalog_type'],
				];

				$results = Composer::productIdAutocompleteRender($query);

				if (!empty($results)) {
					$selected_items .= '<div class="form-control-static"><button type="button" class="delAccessory btn btn-default" name="' . $results[0] . '"><i class="icon-remove text-danger"></i></button>&nbsp;' . $results[1] . '</div>';

					$data_names .= htmlspecialchars($results[1]);
					$data_names .= '¤';
				}

			}

		}

		if (strrpos($this->value, '-') === FALSE && !empty($this->value)) {
			$this->value .= '-';
		}

		$output .= '<div class="input-group">
                    <input type="text" class="vc_auto_complete_param" type="text" placeholder="Click here and start typing..."/>
                    <span class="input-group-addon"><i class="icon-search"></i></span>
                </div>';

		$output .= '<div class="selected-items">' . $selected_items . '</div>';
		$output .= '<input data-names="' . $data_names . '" name="' .
		$this->settings['param_name'] .
		'" class="wpb_vc_param_value  ' .
		$this->settings['param_name'] . ' ' .
		$this->settings['type'] . '_field" type="hidden" value="' . $this->value . '" ' .
			((isset($this->settings['settings']) && !empty($this->settings['settings'])) ? ' data-settings="' . htmlentities(json_encode($this->settings['settings']), ENT_QUOTES, "utf-8") . '" ' : '') .
			' /></div>';

		return $output;
	}

}

function vc_autocomplete_form_field($settings, $value, $tag = false) {

	$auto_complete = new ComposerAutocomplete($settings, $value, $tag);
	return $auto_complete->render();
}
