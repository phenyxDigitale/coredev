<?php
class ComposerShortCode_vc_single_image extends ComposerShortCode {

	public function singleParamHtmlHolder($param, $value) {

		$output = '';
		$old_names = ['yellow_message', 'blue_message', 'green_message', 'button_green', 'button_grey', 'button_yellow', 'button_blue', 'button_red', 'button_orange'];
		$new_names = ['alert-block', 'alert-info', 'alert-success', 'btn-success', 'btn', 'btn-info', 'btn-primary', 'btn-danger', 'btn-warning'];
		$value = str_ireplace($old_names, $new_names, $value);
		

		$param_name = isset($param['param_name']) ? $param['param_name'] : '';
		$type = isset($param['type']) ? $param['type'] : '';
		$class = isset($param['class']) ? $param['class'] : '';

		if (isset($param['holder']) == false || $param['holder'] == 'hidden') {
			$output .= '<input type="hidden" class="wpb_vc_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '" value="' . $value . '" />';

			if (($param['type']) == 'attach_image') {

				$element_icon = $this->settings('icon');
				$thumb_size = '';

				$img = getImageBySize(['attach_id' => (int) preg_replace('/[^\d]/', '', $value), 'thumb_size' => $thumb_size]);

				$this->setSettings('logo', ($img ? $img['thumbnail'] : '<img width="150" height="150" src="/content/backoffice/vc/blank.gif" class="attachment-thumbnail vc_element-icon"  data-name="' . $param_name . '" alt="" title="" style="display: none;" />') . '<span class="no_image_image vc_element-icon' . (!empty($element_icon) ? ' ' . $element_icon : '') . ($img && !empty($img['p_img_large'][0]) ? ' image-exists' : '') . '" /><a href="#" class="column_edit_trigger' . ($img && !empty($img['p_img_large'][0]) ? ' image-exists' : '') . '">' . ephenyx_manager()->l('Add image') . '</a>');
				$output .= $this->outputTitleTrue($this->settings['name']);
			}

		} else {
			$output .= '<' . $param['holder'] . ' class="wpb_vc_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '">' . $value . '</' . $param['holder'] . '>';
		}

		if (!empty($param['admin_label']) && $param['admin_label'] === true) {
			$output .= '<span class="vc_admin_label admin_label_' . $param['param_name'] . (empty($value) ? ' hidden-label' : '') . '"><label>' . $param['heading'] . '</label>: ' . $value . '</span>';
		}

		return $output;
	}

	protected function outputTitle($title) {

		return '';
	}

	protected function outputTitleTrue($title) {

		return '<h4 class="wpb_element_title">' . ephenyx_manager()->l($title) . ' ' . $this->settings('logo') . '</h4>';
	}

}
