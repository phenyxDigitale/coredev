<?php
/**
 */
define('SLIDE_TITLE', ephenyx_manager()->l("Slide"));
class ComposerShortCode_vc_tour extends ComposerShortCode_vc_tabs {

	protected $predefined_atts = [
		'tab_id' => SLIDE_TITLE,
		'title'  => '',
	];

	protected function getFileName() {

		return 'vc_tabs';
	}

	public function getTabTemplate() {

		return '<div class="wpb_template">' . Composer::do_shortcode('[vc_tab title="' . SLIDE_TITLE . '" tab_id=""][/vc_tab]') . '</div>';
	}

}