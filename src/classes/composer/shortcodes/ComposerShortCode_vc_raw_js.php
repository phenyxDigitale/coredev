<?php

class ComposerShortCode_vc_raw_js extends ComposerShortCode_vc_raw_html {

	protected function getFileName() {

		return 'vc_raw_html';
	}
	protected function contentInline($atts, $content = null) {

		$output = $el_class = $width = $el_position = '';
		extract(Composer::shortcode_atts([
			'el_class'    => '',
			'el_position' => '',
			'width'       => '1/2',
		], $atts));

		$el_class = $this->getExtraClass($el_class);
		$el_class .= ' wpb_raw_js';
		$content = rawurldecode(base64_decode(strip_tags($content)));
		$css_class = 'wpb_raw_code' . $el_class;
		$output .= "\n\t" . '<div class="' . $css_class . '">';
		$output .= "\n\t\t" . '<div class="wpb_wrapper">';
		$output .= "\n\t\t\t" . '<input type="hidden" class="vc_js_inline_holder" value="' . ephenyx_manager()->esc_attr($content) . '">';
		$output .= "\n\t\t" . '</div> ' . $this->endBlockComment('.wpb_wrapper');
		$output .= "\n\t" . '</div> ' . $this->endBlockComment('.wpb_raw_code');

		return $output;
	}
}