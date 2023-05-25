<?php
$vc_manager = ephenyx_manager();
$output = $title = $type = $onclick = $custom_links = $img_size = $custom_links_target = $images = $el_class = $interval = '';
extract( Composer::shortcode_atts( array(
	'title' => '',
	'type' => 'flexslider',
	'eventclick' => 'link_image',
	'custom_links' => '',
	'custom_links_target' => '',
	'img_size' => 'thumbnail',
	'images' => '',
	'el_class' => '',
	'onclick' => '',
	'interval' => '5',
), $atts ) );
$gal_images = '';
$link_start = '';
$link_end = '';
$el_start = '';
$el_end = '';
$slides_wrap_start = '';
$slides_wrap_end = '';
$onclick =$eventclick;
$el_class = $this->getExtraClass( $el_class );
if ( $type == 'nivo' ) {
	$type = ' wpb_slider_nivo theme-default';
        if(Configuration::get('vc_load_nivo_js') != 'no'){
            $vc_manager->front_js[] = _EPH_ADMIN_THEME_DIR_. '/composer/nivoslider/jquery.nivo.slider.pack.js';
            Context::getContext()->controller->addJS(_EPH_ADMIN_THEME_DIR_. '/composer/nivoslider/jquery.nivo.slider.pack.js' );
        }
        if(Configuration::get('vc_load_nivo_css') != 'no'){
            $vc_manager->front_css[] = _EPH_ADMIN_THEME_DIR_. '/composer/nivoslider/nivo-slider.css';
        	Context::getContext()->controller->addCSS(_EPH_ADMIN_THEME_DIR_. '/composer/nivoslider/nivo-slider.css' );
    	}
        $vc_manager->front_css[] = _EPH_ADMIN_THEME_DIR_. '/composer/nivoslider/themes/default/default.css';
        Context::getContext()->controller->addCSS(_EPH_ADMIN_THEME_DIR_.'/composer/nivoslider/themes/default/default.css' );
        

	$slides_wrap_start = '<div class="nivoSlider">';
	$slides_wrap_end = '</div>';
} else if ( $type == 'flexslider' || $type == 'flexslider_fade' || $type == 'flexslider_slide' || $type == 'fading' ) {
	$el_start = '<li>';
	$el_end = '</li>';
	$slides_wrap_start = '<ul class="slides">';
	$slides_wrap_end = '</ul>';
	if(Configuration::get('vc_load_flex_css') != 'no'){
        $vc_manager->front_css[] = _EPH_ADMIN_THEME_DIR_. '/composer/flexslider/flexslider.css';
		Context::getContext()->controller->addCSS(_EPH_ADMIN_THEME_DIR_. '/composer/flexslider/flexslider.css');
	}
	if(Configuration::get('vc_load_flex_js') != 'no'){
        $vc_manager->front_js[] = _EPH_ADMIN_THEME_DIR_. '/composer/flexslider/jquery.flexslider-min.js';
		Context::getContext()->controller->addJS(_EPH_ADMIN_THEME_DIR_. '/composer/flexslider/jquery.flexslider-min.js' );
	}

} else if ( $type == 'image_grid' ) {
        $vc_manager->front_js[] = _EPH_ADMIN_THEME_DIR_. '/composer/isotope/dist/isotope.pkgd.min.js';
        Context::getContext()->controller->addJS(_EPH_ADMIN_THEME_DIR_. '/composer/isotope/dist/isotope.pkgd.min.js' );
	$el_start = '<li class="isotope-item">';
	$el_end = '</li>';
	$slides_wrap_start = '<ul class="wpb_image_grid_ul">';
	$slides_wrap_end = '</ul>';
}

if ( $eventclick == 'link_image' ) {
        $vc_manager->front_css[] = _EPH_ADMIN_THEME_DIR_. '/composer/prettyphoto/css/prettyPhoto.css';
        $vc_manager->front_js[] = _EPH_ADMIN_THEME_DIR_. '/composer/prettyphoto/js/jquery.prettyPhoto.js';
        Context::getContext()->controller->addCSS(_EPH_ADMIN_THEME_DIR_. '/composer/prettyphoto/css/prettyPhoto.css');
        Context::getContext()->controller->addJS(_EPH_ADMIN_THEME_DIR_. '/composer/prettyphoto/js/jquery.prettyPhoto.js');
}

$flex_fx = '';
if ( $type == 'flexslider' || $type == 'flexslider_fade' || $type == 'fading' ) {
	$type = ' wpb_flexslider flexslider_fade flexslider';
	$flex_fx = ' data-flex_fx="fade"';
} else if ( $type == 'flexslider_slide' ) {
	$type = ' wpb_flexslider flexslider_slide flexslider';
	$flex_fx = ' data-flex_fx="slide"';
} else if ( $type == 'image_grid' ) {
	$type = ' wpb_image_grid';
}


if ( $images == '' ) $images = '-1,-2,-3';

$pretty_rel_random = ' rel="prettyPhoto[rel-' . rand() . ']"'; //rel-'.rand();

if ( $onclick == 'custom_link' ) {
	$custom_links = explode( ',', $custom_links );
}
// var_dump($onclick =$eventclick);
$images = explode( ',', $images );
$i = - 1;

foreach ( $images as $attach_id ) {
	$i ++;
	if ( $attach_id > 0 ) {
		$post_thumbnail = getImageBySize( array( 'attach_id' => $attach_id, 'thumb_size' => $img_size ) );
	} else {
		$post_thumbnail = array();
	}

	$thumbnail = isset($post_thumbnail['thumbnail']) ? $post_thumbnail['thumbnail'] : '';
	$p_img_large = isset($post_thumbnail['p_img_large']) ? $post_thumbnail['p_img_large'] : '';
	$link_start = $link_end = '';

	if ( $onclick == 'link_image' ) {
		$link_start = '<a class="prettyphoto" href="' . $p_img_large . '"' . $pretty_rel_random . '>';
		$link_end = '</a>';
	} else if ( $onclick == 'custom_link' && isset( $custom_links[$i] ) && $custom_links[$i] != '' ) {
		$link_start = '<a href="' . $custom_links[$i] . '"' . ( ! empty( $custom_links_target ) ? ' target="' . $custom_links_target . '"' : '' ) . '>';
		$link_end = '</a>';
	}
	$gal_images .= $el_start . $link_start . $thumbnail . $link_end . $el_end;
}
$css_class =  'wpb_gallery wpb_content_element' . $el_class . ' vc_clearfix';
$output .= "\n\t" . '<div class="' . $css_class . '">';
$output .= "\n\t\t" . '<div class="wpb_wrapper">';
$output .= widget_title( array( 'title' => $title, 'extraclass' => 'wpb_gallery_heading' ) );
$output .= '<div class="wpb_gallery_slides' . $type . '" data-interval="' . $interval . '"' . $flex_fx . '>' . $slides_wrap_start . $gal_images . $slides_wrap_end . '</div>';
$output .= "\n\t\t" . '</div> ' . $this->endBlockComment( '.wpb_wrapper' );
$output .= "\n\t" . '</div> ' . $this->endBlockComment( '.wpb_gallery' );

echo $output;