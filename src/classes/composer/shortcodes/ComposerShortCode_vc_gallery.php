<?php
class ComposerShortCode_vc_gallery extends ComposerShortCode {

	public function singleParamHtmlHolder($param, $value) {

		$output = '';
		
		$old_names = ['yellow_message', 'blue_message', 'green_message', 'button_green', 'button_grey', 'button_yellow', 'button_blue', 'button_red', 'button_orange'];
		$new_names = ['alert-block', 'alert-info', 'alert-success', 'btn-success', 'btn', 'btn-info', 'btn-primary', 'btn-danger', 'btn-warning'];
		$value = str_ireplace($old_names, $new_names, $value);
		
		$param_name = isset($param['param_name']) ? $param['param_name'] : '';
		$type = isset($param['type']) ? $param['type'] : '';
		$class = isset($param['class']) ? $param['class'] : '';

		if (isset($param['holder']) == true && $param['holder'] !== 'hidden') {
			$output .= '<' . $param['holder'] . ' class="wpb_vc_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '">' . $value . '</' . $param['holder'] . '>';
		}

		if ($param_name == 'images') {
			$images_ids = empty($value) ? [] : explode(',', trim($value));
			$output .= '<ul class="attachment-thumbnails' . (empty($images_ids) ? ' image-exists' : '') . '" data-name="' . $param_name . '">';

			foreach ($images_ids as $image) {
				$img = getImageBySize(['attach_id' => (int) $image, 'thumb_size' => 'thumbnail']);
				$output .= ($img ? '<li>' . $img['thumbnail'] . '</li>' : '<li><img width="150" height="150" test="' . $image . '" src="/content/backoffice/vc/blank.gif" class="attachment-thumbnail" alt="" title="" /></li>');
			}

			$output .= '</ul>';
			$output .= '<a href="#" class="column_edit_trigger' . (!empty($images_ids) ? ' image-exists' : '') . '">' . ephenyx_manager()->l('Add images') . '</a>';

		}

		return $output;
	}

}
