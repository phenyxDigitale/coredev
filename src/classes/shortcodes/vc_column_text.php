<?php
$output = $el_class = $css_animation = '';

extract(vc_map_get_attributes( $this->getShortcode(), $atts ));

$el_class = $this->getExtraClass($el_class);

$css_class = 'wpb_text_column wpb_content_element ' . $el_class . shortcode_custom_css_class( $css, ' ' );
if(isset($css) && !empty($css)){
	$css_out = '<style>'.$css.'</style>';
	$output .= $css_out;
}
$css_class .= $this->getCSSAnimation($css_animation);
$output .= "\n\t".'<div class="'.$css_class.'">';
$output .= "\n\t\t".'<div class="wpb_wrapper">';
$output .= "\n\t\t\t".js_remove_wpautop($content, true);
$output .= "\n\t\t".'</div> ' . $this->endBlockComment('.wpb_wrapper');
$output .= "\n\t".'</div> ' . $this->endBlockComment('.wpb_text_column');

echo $output;