<?php

$vc_manager = ephenyx_manager();
extract(Composer::shortcode_atts(array(
    'h2' => '',
    'h4' => '',
    'position' => '',
    'el_width' => '',
    'style' => '',
    'txt_align' => '',
    'accent_color' => '',
    'link' => '',
    'title' => $this->l('Text on the button'),
    'color' => '',
    'icon' => '',
    'size' => '',
    'btn_style' => '',
    'el_class' => '',
    'css_animation' => ''
), $atts));

$class = "vc_call_to_action wpb_content_element";
// $position = 'left';
// $width = '90';
// $style = '';
// $txt_align = 'right';
$link = ($link=='||') ? '' : $link;

$class .= ($position!='') ? ' vc_cta_btn_pos_'.$position : '';
$class .= ($el_width!='') ? ' vc_el_width_'.$el_width : '';
$class .= ($color!='') ? ' vc_cta_'.$color : '';
$class .= ($style!='') ? ' vc_cta_'.$style : '';
$class .= ($txt_align!='') ? ' vc_txt_align_'.$txt_align : '';

$inline_css = ($accent_color!='') ? ' style="'.get_css_color('background-color', $accent_color).get_css_color('border-color', $accent_color).'"' : '';

$class .= $this->getExtraClass($el_class);
$css_class =  $class;
$css_class .= $this->getCSSAnimation($css_animation);
?>
<div<?php echo $inline_css; ?> class="<?php echo $vc_manager->esc_attr(trim($css_class)); ?>">
    <?php if ($link!='' && $position!='bottom') echo Composer::do_shortcode('[vc_button2 link="'.$link.'" title="'.$title.'" color="'.$color.'" icon="'.$icon.'" size="'.$size.'" style="'.$btn_style.'" el_class="vc_cta_btn"]'); ?>
<?php if ($h2!='' || $h4!=''): ?>
    <hgroup>
        <?php if ($h2!=''): ?><h2 class="wpb_heading"><?php echo $h2; ?></h2><?php endif; ?>
        <?php if ($h4!=''): ?><h4 class="wpb_heading"><?php echo $h4; ?></h4><?php endif; ?>
    </hgroup>
<?php endif; ?>
    <?php echo js_remove_wpautop($content, true); ?>
    <?php if ($link!='' && $position=='bottom') echo Composer::do_shortcode('[vc_button2 link="'.$link.'" title="'.$title.'" color="'.$color.'" icon="'.$icon.'" size="'.$size.'" style="'.$btn_style.'" el_class="vc_cta_btn"]'); ?>
</div>
<?php $this->endBlockComment('.vc_call_to_action') . "\n";
