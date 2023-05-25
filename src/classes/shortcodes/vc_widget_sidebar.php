<?php
$output = $el_position = $title = $width = $el_class = $sidebar_id = '';
extract(Composer::shortcode_atts(array(
    'el_position' => '',
    'title' => '',
    'width' => '1/1',
    'el_class' => '',
    'sidebar_id' => ''
), $atts));
if ( $sidebar_id == '' ) return null;

$el_class = $this->getExtraClass($el_class);

ob_start();
dynamic_sidebar($sidebar_id);
$sidebar_value = ob_get_contents();
ob_end_clean();

$sidebar_value = trim($sidebar_value);
$sidebar_value = (substr($sidebar_value, 0, 3) == '<li' ) ? '<ul>'.$sidebar_value.'</ul>' : $sidebar_value;
//
$css_class =  'wpb_widgetised_column wpb_content_element' . $el_class;
$output .= "\n\t".'<div class="'.$css_class.'">';
$output .= "\n\t\t".'<div class="wpb_wrapper">';
$output .= widget_title(array('title' => $title, 'extraclass' => 'wpb_widgetised_column_heading'));
$output .= "\n\t\t\t".$sidebar_value;
$output .= "\n\t\t".'</div> '.$this->endBlockComment('.wpb_wrapper');
$output .= "\n\t".'</div> '.$this->endBlockComment('.wpb_widgetised_column');

echo $output;