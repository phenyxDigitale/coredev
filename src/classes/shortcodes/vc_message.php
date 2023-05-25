<?php
$output = $color = $el_class = $css_animation = '';
extract(Composer::shortcode_atts(array(
    'color' => 'alert-info',
    'el_class' => '',
    'style' => '',
    'css_animation' => ''
), $atts));
$el_class = $this->getExtraClass($el_class);

$class = "";
//$style = "square_outlined";
$class .= ($style!='') ? ' vc_alert_'.$style : '';
$class .= ( $color != '' && $color != "alert-block") ? ' wpb_'.$color : '';

$css_class =  'wpb_alert wpb_content_element ' . $class . $el_class;

$css_class .= $this->getCSSAnimation($css_animation);
?>
<div class="<?php echo $css_class; ?>">
	<div class="messagebox_text"><?php echo js_remove_wpautop($content, true); ?></div>
</div>
<?php echo $this->endBlockComment('alert box')."\n";