<?php
$output = $el_class = $el_id = '';
extract(Composer::shortcode_atts(array(
    'el_class' => '',
    'el_id' => '',
    'css_animation' => ''
), $atts));

$el_class = $this->getExtraClass($el_class);
$el_class .= ($this->settings['base']=='vc_raw_code') ? ' wpb_content_element wpb_raw_code' : ' wpb_raw_js';

$css_class =  'wpb_raw_code' . $el_class;
$css_class .= $this->getCSSAnimation( $css_animation );
$code_id = '';
if ( ! empty( $el_id ) ) {
	$code_id = 'id="ace_' . Tools::htmlentitiesUTF8( $el_id ) . '"';
}
$ajax = Tools::getValue('ajax', 0);
$output .= "\n\t".'<div class="'.$css_class.'">';
$output .= "\n\t\t".'<div class="wpb_wrapper ace-editor" '.$code_id.'>';
$output .= "\n\t\t\t".$content;
$output .= "\n\t\t".'</div> '.$this->endBlockComment('.wpb_wrapper');
$output .= "\n\t".'</div> '.$this->endBlockComment('.wpb_raw_code');
$output .= "\n".'<script type="text/javascript">';
$output .= "\n\t".'$(document).ready(function(){';
$output .= "\n\t\t".'initComposerAce("ace_' . Tools::htmlentitiesUTF8( $el_id ) . '", '.$ajax.');';
$output .= "\n\t".'})';
$output .= "\n".'</script>';

echo $output;