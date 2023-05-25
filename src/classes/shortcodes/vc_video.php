<?php
$output = $title = $link = $size = $el_class = '';
extract( Composer::shortcode_atts( array(
	'title' => '',
	'link' => 'http://vimeo.com/92033601',
	'size' => ( isset( $content_width ) ) ? $content_width : 500,
	'el_class' => '',
	'css' => ''

), $atts ) );

if ( $link == '' ) {
	return null;
}
$el_class = $this->getExtraClass( $el_class );

$parse_link = parse_url($link);
if(is_array($parse_link)){
	$youtube_link = array('youtube.com','www.youtube.com');
	if(in_array(strtolower($parse_link['host']), $youtube_link)){ // for youtube;
		if($parse_link['path'] == '/watch'){
			$query_str = parse_url($link, PHP_URL_QUERY);
			parse_str($query_str, $query_params);
			$link = $parse_link['scheme'] . '://' . $parse_link['host'] . '/embed/' . $query_params['v'];
		} else {
			$link = $parse_link['scheme'] . '://' . $parse_link['host'] . $parse_link['path'];
		}
	}
	$vimeo_link = array('vimeo.com','www.vimeo.com');
	if(in_array(strtolower($parse_link['host']), $vimeo_link)){ // for vimeo;
		$link = $parse_link['scheme'] . '://player.vimeo.com' . '/video' . $parse_link['path'];
	}
}

$video_w = ( isset( $content_width ) ) ? $content_width : 500;
$video_h = $video_w / 1.61; 
$fullscreen = 'allowfullscreen mozallowfullscreen webkitallowfullscreen';
$embed = '<iframe '.$fullscreen.' width="' . $video_w . '" height="' . $video_h . '" src="' . $link . '"></iframe>';

$css_class =  'wpb_video_widget wpb_content_element' . $el_class . $el_class . shortcode_custom_css_class( $css, $this->settings['base'], $atts );

if(isset($css) && !empty($css)){
	$css_out = '<style>'.$css.'</style>';
	$output .= $css_out;
}

$output .= "\n\t" . '<div class="' . $css_class . '">';
$output .= "\n\t\t" . '<div class="wpb_wrapper">';
$output .= widget_title( array( 'title' => $title, 'extraclass' => 'wpb_video_heading' ) );
$output .= '<div class="wpb_video_wrapper">' . $embed . '</div>';
$output .= "\n\t\t" . '</div> ' . $this->endBlockComment( '.wpb_wrapper' );
$output .= "\n\t" . '</div> ' . $this->endBlockComment( '.wpb_video_widget' );

echo $output;