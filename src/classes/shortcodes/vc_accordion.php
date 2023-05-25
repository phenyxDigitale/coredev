<?php
//wp_enqueue_script('jquery-ui-accordion');
Context::getContext()->controller->addJqueryUI('ui.accordion');

$output = $title = $interval = $el_class = $collapsible = $active_tab = '';
//
extract(Composer::shortcode_atts(array(
    'title' => '',
    'interval' => 0,
    'el_class' => '',
    'collapsible' => 'no',
    'active_tab' => '1'
), $atts));

$el_class = $this->getExtraClass($el_class);
$css_class =  'wpb_accordion wpb_content_element ' . $el_class . ' not-column-inherit';

$output .= "\n\t".'<div class="'.$css_class.'" data-collapsible='.$collapsible.' data-active-tab="'.$active_tab.'">'; //data-interval="'.$interval.'"
$output .= "\n\t\t".'<div class="wpb_wrapper wpb_accordion_wrapper ui-accordion">';
$output .= widget_title(array('title' => $title, 'extraclass' => 'wpb_accordion_heading'));

$output .= "\n\t\t\t".js_remove_wpautop($content);
$output .= "\n\t\t".'</div> '.$this->endBlockComment('.wpb_wrapper');
$output .= "\n\t".'</div> '.$this->endBlockComment('.wpb_accordion');

echo $output;