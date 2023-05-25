<?php

$el_class = $full_height = $full_width = $equal_height = $flex_row = $columns_placement = $content_placement = $parallax = $parallax_image = $css = $el_id = $video_bg = $video_bg_url = $video_bg_parallax = '';
$output = $after_output = '';

$atts = map_get_attributes( $this->getShortcode(), $atts );
extract($atts);

//Context::getContext()->controller->addJS(_EPH_JS_DIR_ . 'composer/composer_front.js');

$el_class = $this->getExtraClass($el_class);

if(isset($css) && !empty($css)){
	$css_out = '<style>'.$css.'</style>';
	$output .= $css_out;
}

$css_classes = array(
	'vc_row',
	'wpb_row', //deprecated
	'vc_row-fluid',
	$el_class,
	shortcode_custom_css_class( $css ),
);

if (shortcode_custom_css_has_property( $css, array('border', 'background') ) || $video_bg || $parallax) {
	$css_classes[]='vc_row-has-fill';
}

if (!empty($atts['gap'])) {
	$css_classes[] = 'vc_column-gap-'.$atts['gap'];
}

$wrapper_attributes = array();
if ( ! empty( $el_id ) ) {
	$wrapper_attributes[] = 'id="' . Tools::htmlentitiesUTF8( $el_id ) . '"';
}

if ( ! empty( $full_width ) ) {
	$wrapper_attributes[] = 'data-vc-full-width="true"';
	$wrapper_attributes[] = 'data-vc-full-width-init="false"';
	if ( 'stretch_row_content' === $full_width ) {
		$wrapper_attributes[] = 'data-vc-stretch-content="true"';
	} elseif ( 'stretch_row_content_no_spaces' === $full_width ) {
		$wrapper_attributes[] = 'data-vc-stretch-content="true"';
		$css_classes[] = 'vc_row-no-padding';
	}
	$after_output .= '<div class="vc_row-full-width"></div>';
}

if ( ! empty( $full_height ) ) {
	$css_classes[] = ' vc_row-o-full-height';
	if ( ! empty( $columns_placement ) ) {
		$flex_row = true;
		$css_classes[] = ' vc_row-o-columns-' . $columns_placement;
	}
}

if ( ! empty( $equal_height ) ) {
	$flex_row = true;
	$css_classes[] = ' vc_row-o-equal-height';
}

if ( ! empty( $content_placement ) ) {
	$flex_row = true;
	$css_classes[] = ' vc_row-o-content-' . $content_placement;
}

if ( ! empty( $flex_row ) ) {
	$css_classes[] = ' vc_row-flex';
}

$has_video_bg = ( ! empty( $video_bg ) && ! empty( $video_bg_url ) && extract_youtube_id( $video_bg_url ) );

if ( $has_video_bg ) {
	$parallax = $video_bg_parallax;
	$parallax_image = $video_bg_url;
	$css_classes[] = ' vc_video-bg-container';
    Context::getContext()->controller->addJS('https://www.youtube.com/iframe_api');
}

if ( ! empty( $parallax ) ) {
    Context::getContext()->controller->addJS(_EPH_ADMIN_THEME_DIR_.'/composer/bower/skrollr/dist/skrollr.min.js');
	$wrapper_attributes[] = 'data-vc-parallax="1.5"'; 
	$css_classes[] = 'vc_general vc_parallax vc_parallax-' . $parallax;
	if ( false !== strpos( $parallax, 'fade' ) ) {
		$css_classes[] = 'js-vc_parallax-o-fade';
		$wrapper_attributes[] = 'data-vc-parallax-o-fade="on"';
	} elseif ( false !== strpos( $parallax, 'fixed' ) ) {
		$css_classes[] = 'js-vc_parallax-o-fixed';
	}
}

if ( ! empty( $parallax_image ) ) {
	if ( $has_video_bg ) {
		$parallax_image_src = $parallax_image;
	} else {
		$parallax_image_id = preg_replace( '/[^\d]/', '', $parallax_image );
		$parallax_image_src = Composer::getFullImageUrl( $parallax_image_id );
	}
	$wrapper_attributes[] = 'data-vc-parallax-image="' . Tools::htmlentitiesUTF8( $parallax_image_src ) . '"';
}
if ( ! $parallax && $has_video_bg ) {
	$wrapper_attributes[] = 'data-vc-video-bg="' . Tools::htmlentitiesUTF8( $video_bg_url ) . '"';
}



$hook_args = array('atts'=>$atts, 'base' => $this->settings['base'], 'css_classes' => '');
$css_class = implode( ' ', array_filter( $css_classes ));
$css_class .= preg_replace( '/\s+/', ' ', Hook::exec('VcShortcodesCssClass', $hook_args)) ;
$wrapper_attributes[] = 'class="' . Tools::htmlentitiesUTF8( trim( $css_class ) ) . '"';

$output .= '<div '.implode( ' ', $wrapper_attributes ).'>';
$output .= js_remove_wpautop($content);
$output .= '</div>'.$after_output.$this->endBlockComment('row');

echo $output;