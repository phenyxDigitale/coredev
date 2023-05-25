<?php

$vc = ephenyx_manager();

extract( Composer::shortcode_atts( array(
	'id' => '',
	'title' => 'Text on the button',
    'size' => '',
	'color' => '',
	'style' => '',
    'position' => 'align_center',
    'css_animation' => ''
), $atts ) );
$class = 'vc_btn';

$color = ( $color != '' ) ? ' wpb_' . $color : '';
$class .= ( $color != '' ) ? ( ' vc_btn_' . $color . ' vc_btn-' . $color ) : '';
$size = ( $size != '' && $size != 'wpb_regularsize' ) ? ' wpb_' . $size : ' ' . $size;
$position = ( $position != '' ) ? ' ' . $position  : '';
$css_class =  'vc_btn ' . $position;
$css_class .= $this->getCSSAnimation( $css_animation );
$button = '<span class="vc_btn ' . $color . $size  . '">' . $title  . '</span>';
$button = '<button class="wpb_'.$size.' ' . $class . '" onClick="openAjaxCms('.$id.')">' . $button . '</button>';
$output = '<div class="' . $css_class . ' col-lg-12">';
$output .= $button;
$output .= '</div> ' . $this->endBlockComment( 'button' ) . "\n";

echo $output;
