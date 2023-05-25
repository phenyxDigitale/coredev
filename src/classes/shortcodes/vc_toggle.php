<?php
$output = $title = $el_class = $open = $css_animation = '';
extract(Composer::shortcode_atts(array(
    'title' => "Click to toggle",
    'el_class' => '',
    'open' => 'false',
    'css_animation' => ''
), $atts));

$el_class = $this->getExtraClass($el_class);
$open = ( $open == 'true' ) ? ' wpb_toggle_title_active' : '';
$el_class .= ( $open == ' wpb_toggle_title_active' ) ? ' wpb_toggle_open' : '';

$css_class =  'wpb_toggle' . $open;
$css_class .= $this->getCSSAnimation($css_animation);

$output .=  '<h4 class="'.$css_class.'">'.$title.'</h4>';
$css_class =  'wpb_toggle_content' . $el_class;
$output .= '<div class="'.$css_class.'">'.js_remove_wpautop($content, true).'</div>'.$this->endBlockComment('toggle')."\n";

echo $output;