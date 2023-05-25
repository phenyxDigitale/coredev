<?php
$vc_manager = ephenyx_manager();

$output = $title = $tabs_mode = $el_class = '';
extract( Composer::shortcode_atts( array(
	'title' => '',
	'tabs_mode' => 0,
	'el_class' => ''
), $atts ) );

$el_class = $this->getExtraClass( $el_class );
$element = 'wpb_tabs';
preg_match_all( '/vc_tab([^\]]+)/i', $content, $matches, PREG_OFFSET_CAPTURE );
$tab_titles = array();
if ( isset( $matches[1] ) ) {
    $tab_titles = $matches[1];
}
switch ($tabs_mode) {
    case 0:
        
        if ( 'vc_tour' == $this->shortcode ) $element = 'wpb_tour';

        
        $tabs_nav = '<div id="content_tabs" class="Tabs">';
        $tabs_nav .= '<ul class="wpb_tabs_nav ui-tabs-nav vc_clearfix">';
        foreach ( $tab_titles as $tab ) {
            $tab_atts = Composer::shortcode_parse_atts($tab[0]);
            $href = isset( $tab_atts['tab_id'] ) ? $tab_atts['tab_id'] : Tools::safeOutput( $tab_atts['title'] );
            if(isset($tab_atts['title'])) {
                $tabs_nav .= '<li id="li_' . $href. '"><a href="#tab-' . $href. '">' . $tab_atts['title'] . '</a></li>';
            }
        }
        $tabs_nav .= '</ul>' . "\n".'<div id="tabs-content" class="tabs-controller-content">'. "\n";

        $css_class =  trim( $element . ' wpb_content_element ' . $el_class );

        $output .= "\n\t" . '<div class="' . $css_class . '">';
        $output .= "\n\t\t" . '<div class="wpb_wrapper wpb_tour_tabs_wrapper ui-tabs vc_clearfix">';
        $output .= widget_title( array( 'title' => $title, 'extraclass' => $element . '_heading' ) );
        $output .= "\n\t\t\t" . $tabs_nav;
        $output .= "\n\t\t\t" . js_remove_wpautop( $content );
        if ( 'vc_tour' == $this->shortcode ) {
            $output .= "\n\t\t\t" . '<div class="wpb_tour_next_prev_nav vc_clearfix"> <span class="wpb_prev_slide"><a href="#prev" title="' . $this->l('Previous tab') . '">' . $this->l('Previous tab') . '</a></span> <span class="wpb_next_slide"><a href="#next" title="' . $this->l('Next tab') . '">' . $this->l('Next tab') . '</a></span></div>';
        }
        $output .= "\n\t\t" . '</div> ' . $this->endBlockComment( '.wpb_wrapper' );
        $output .= "\n\t" . '</div></div></div>' . $this->endBlockComment( $element );
        $output .= '<script type="text/javascript">
		  $(document).ready(function(){
          $("#content_tabs").tabs({
			show: {
				effect: "blind", 
				duration: 800
			}
          });
    </script>';
        break;
    case 1:
        
        $index = [];
        foreach ( $tab_titles as $key =>$tab ) {
            
           $tab_atts = Composer::shortcode_parse_atts($tab[0]);
           $href = isset( $tab_atts['tab_id'] ) ? $tab_atts['tab_id'] : Tools::safeOutput( $tab_atts['title'] );
           $index[] =  'tab-'.$href;
                     
        }
        
        $index = Tools::jsonEncode($index);
        $css_class =  trim( $element . ' wpb_content_element ' . $el_class );

        $output .= "\n\t" . '<div class="' . $css_class . '">';
        $output .= "\n\t\t" . '<div class="wpb_wrapper wpb_tour_tabs_wrapper ui-tabs vc_clearfix">';
        $output .= widget_title( array( 'title' => $title, 'extraclass' => $element . '_heading' ) );
       
        $output .= "\n\t\t\t" . js_remove_wpautop( $content );
        $output .= "\n\t\t\t" . '<div class="wp_navigate vc_clearfix"> <span class="wpb_prev_slide"><button class="btn" onClick="showPreviousTab()"><i class="fa-duotone fa-chevron-left"></i>' . $this->l('Previous tab') . '</button></span> <span class="wpb_next_slide"><button class="btn" onClick="showNextTab()">' . $this->l('Next tab') . '</a><i class="fa-duotone fa-angle-right"></i></span></div>';
       
        $output .= "\n\t\t" . '</div> ' . $this->endBlockComment( '.wpb_wrapper' );
        $output .= "\n\t" . '</div>' . $this->endBlockComment( $element );
        $output .= '<script type="text/javascript">
		  var tabs = '.$index.';
          $(document).ready(function(){
          $(".wpb_prev_slide").addClass("hidden");
            $.each(tabs , function( index, value ) {                
                if(index > 0) {                
                    $("#"+value).addClass("hidden");
                } else {
                    $("#"+value).addClass("active");
                }            
            });
        })
        </script>';
        break;
}



echo $output;