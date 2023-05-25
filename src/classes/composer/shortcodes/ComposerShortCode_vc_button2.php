<?php


class ComposerShortCode_vc_button2 extends ComposerShortCode {

	protected function outputTitle($title) {

		$icon = $this->settings('icon');
		return '<h4 class="wpb_element_title"><span class="vc_element-icon' . (!empty($icon) ? ' ' . $icon : '') . '"></span></h4>';
	}
}