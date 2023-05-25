<?php
$output = $font_color = $el_class = $width = $offset = '';
extract(Composer::shortcode_atts(array(
	'font_color'      => '',
    'el_class' => '',
    'width' => '1/1',
    'css' => '',
	'offset' => ''
), $atts));

$el_class = $this->getExtraClass($el_class);
$width = translateColumnWidthToSpan($width);
$width = column_offset_class_merge($offset, $width);
$el_class .= ' wpb_column vc_column_container';
$style = $this->buildStyle( $font_color );
$css_class = $width . $el_class . shortcode_custom_css_class( $css, ' ' );
if(isset($css) && !empty($css)){
	$css_out = '<style>'.$css.'</style>';
	$output .= $css_out;
}
$output .= "\n\t".'<div class="'.$css_class.'"'.$style.'>';
$output .= "\n\t\t".'<div class="wpb_wrapper">';
$output .= "\n\t\t\t".js_remove_wpautop($content);
$output .= "\n\t\t".'</div> '.$this->endBlockComment('.wpb_wrapper');
$output .= "\n\t".'</div> '.$this->endBlockComment($el_class) . "\n";

echo $output;