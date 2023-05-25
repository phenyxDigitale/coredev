<?php
$output = $el_class = $width = '';
extract(Composer::shortcode_atts(array(
    'el_class' => '',
), $atts));

$el_class = $this->getExtraClass($el_class);

$el_class .= (!empty($el_class) ? ' ' : '').'wpb_item items_container';

$css_class =  $el_class;
$output .= "\n\t".'<div class="'.$css_class.'">';
$output .= "\n\t\t".'<div class="wpb_wrapper">';
$output .= "\n\t\t\t".js_remove_wpautop($content);
$output .= "\n\t\t".'</div> '.$this->endBlockComment('.wpb_wrapper');
$output .= "\n\t".'</div> '.$this->endBlockComment($el_class) . "\n";

echo $output;