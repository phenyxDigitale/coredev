<?php
$vc_manager = ephenyx_manager();
$output = $color = $size = $icon = $target = $href = $el_class = $title = $position = '';
extract( Composer::shortcode_atts( array(
	'color' => 'wpb_button',
	'size' => '',
	'icon' => 'none',
	'target' => '_self',
	'href' => '',
	'el_class' => '',
	'title' => 'Text on the button',
	'position' => ''
), $atts ) );
$a_class = '';

if ( $el_class != '' ) {
	$tmp_class = explode( " ", strtolower( $el_class ) );
	$tmp_class = str_replace( ".", "", $tmp_class );
	if ( in_array( "prettyphoto", $tmp_class ) ) {
        $vc_manager->front_css[] = _EPH_ADMIN_THEME_DIR_. '/composer/prettyphoto/css/prettyPhoto.css';
        $vc_manager->front_js[] = _EPH_ADMIN_THEME_DIR_. '/composer/prettyphoto/js/jquery.prettyPhoto.js';
        Context::getContext()->controller->addCSS(_EPH_ADMIN_THEME_DIR_. '/composer/prettyphoto/css/prettyPhoto.css');
        Context::getContext()->controller->addJS(_EPH_ADMIN_THEME_DIR_. '/composer/prettyphoto/js/jquery.prettyPhoto.js');
                
		$a_class .= ' prettyphoto';
		$el_class = str_ireplace( "prettyphoto", "", $el_class );
	}
	if ( in_array( "pull-right", $tmp_class ) && $href != '' ) {
		$a_class .= ' pull-right';
		$el_class = str_ireplace( "pull-right", "", $el_class );
	}
	if ( in_array( "pull-left", $tmp_class ) && $href != '' ) {
		$a_class .= ' pull-left';
		$el_class = str_ireplace( "pull-left", "", $el_class );
	}
}

if ( $target == 'same' || $target == '_self' ) {
	$target = '';
}
$target = ( $target != '' ) ? ' target="' . $target . '"' : '';

$color = ( $color != '' ) ? ' wpb_' . $color : '';
$size = ( $size != '' && $size != 'wpb_regularsize' ) ? ' wpb_' . $size : ' ' . $size;
$icon = ( $icon != '' && $icon != 'none' ) ? ' ' . $icon : '';
$i_icon = ( $icon != '' ) ? ' <i class="icon"> </i>' : '';
$position = ( $position != '' ) ? ' ' . $position . '-button-position' : '';
$el_class = $this->getExtraClass( $el_class );

$css_class =  'wpb_button ' . $color . $size . $icon . $el_class . $position;

if ( $href != '' ) {
	$output .= '<span class="' . $css_class . '">' . $title . $i_icon . '</span>';
	$output = '<a class="wpb_button_a' . $a_class . '" title="' . $title . '" href="' . $href . '"' . $target . '>' . $output . '</a>';
} else {
	$output .= '<button class="' . $css_class . '">' . $title . $i_icon . '</button>';

}

echo $output . $this->endBlockComment( 'button' ) . "\n";