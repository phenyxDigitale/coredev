<?php
global $vc_manager;

$title = $el_class = $value = $label_value= $units = '';
extract(Composer::shortcode_atts(array(
'title' => '',
'el_class' => '',
'value' => '50',
'units' => '',
'color' => 'turquoise',
'label_value' => ''
), $atts));

Context::getContext()->controller->addJS(_EPH_ADMIN_THEME_DIR_.'/composer/progress-circle/ProgressCircle.js');
Context::getContext()->controller->addJS(_EPH_ADMIN_THEME_DIR_.'/composer/vc_chart/jquery.vc_chart.js');
//wp_enqueue_script('vc_pie');

$el_class = $this->getExtraClass( $el_class );
$css_class =  'vc_pie_chart wpb_content_element' . $el_class;
$output = "\n\t".'<div class= "'.$css_class.'" data-pie-value="'.$value.'" data-pie-label-value="'.$label_value.'" data-pie-units="'.$units.'" data-pie-color="'.htmlspecialchars($color).'">';
    $output .= "\n\t\t".'<div class="wpb_wrapper">';
        $output .= "\n\t\t\t".'<div class="vc_pie_wrapper">';
            $output .= "\n\t\t\t".'<span class="vc_pie_chart_back"></span>';
            $output .= "\n\t\t\t".'<span class="vc_pie_chart_value"></span>';
            $output .= "\n\t\t\t".'<canvas width="101" height="101"></canvas>';
            $output .= "\n\t\t\t".'</div>';
        if ($title!='') {
        $output .= '<h4 class="wpb_heading wpb_pie_chart_heading">'.$title.'</h4>';
        }
        //wpb_widget_title(array('title' => $title, 'extraclass' => 'wpb_pie_chart_heading'));
    $output .= "\n\t\t".'</div>'.$this->endBlockComment('.wpb_wrapper');
    $output .= "\n\t".'</div>'.$this->endBlockComment('.wpb_pie_chart')."\n";

echo $output;