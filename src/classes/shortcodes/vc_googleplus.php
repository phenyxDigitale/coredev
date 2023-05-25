<?php
$type = $annotation = '';
extract(Composer::shortcode_atts(array(
    'type' => '',
    'annotation' => ''
), $atts));

$params = '';
$params .= ( $type != '' ) ? ' size="'.$type.'" ' : '';
$params .= ( $annotation != '' ) ? ' annotation="'.$annotation.'"' : '';

if ( $type == '' ) $type = 'standard';
$css_class =  'wpb_googleplus wpb_content_element wpb_googleplus_type_' . $type;
$output = '<div class="'.$css_class.'"><g:plusone'.$params.'></g:plusone></div>'.$this->endBlockComment('wpb_googleplus')."\n";

echo $output;