<?php
$output = $title = '';
$vc_manager = ephenyx_manager();
extract(Composer::shortcode_atts(array(
	'title' => "Section"
), $atts));

$css_class =  'wpb_accordion_section group';
$output .= "\n\t\t\t" . '<div class="'.$css_class.'">';
    $output .= "\n\t\t\t\t" . '<h3 class="wpb_accordion_header ui-accordion-header"><a href="#'.Tools::safeOutput($title).'">'.$title.'</a></h3>';
    $output .= "\n\t\t\t\t" . '<div class="wpb_accordion_content ui-accordion-content vc_clearfix">';
        $output .= ($content=='' || $content==' ') ? $this->l("Empty section. Edit page to add content here.") : "\n\t\t\t\t" . js_remove_wpautop($content);
        $output .= "\n\t\t\t\t" . '</div>';
    $output .= "\n\t\t\t" . '</div> ' . $this->endBlockComment('.wpb_accordion_section') . "\n";

echo $output;