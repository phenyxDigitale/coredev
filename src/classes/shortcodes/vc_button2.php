<?php

$vc = ephenyx_manager();

extract( Composer::shortcode_atts( array(
	'link' => '',
	'title' => 'Text on the button',
	'color' => '',
	'icon' => '',
	'size' => '',
	'style' => '',
	'el_class' => ''
), $atts ) );

$class = 'vc_btn';
//parse link
$link = ( $link == '||' ) ? '' : $link;
$link = vc_build_link( $link );
$a_href = $link['url'];
$a_title = $link['title'];
$a_target = $link['target'];

$class .= ( $color != '' ) ? ( ' vc_btn_' . $color . ' vc_btn-' . $color ) : '';
$class .= ( $size != '' ) ? ( ' vc_btn_' . $size . ' vc_btn-' . $size ) : '';
$class .= ( $style != '' ) ? ' vc_btn_' . $style : '';

$el_class = $this->getExtraClass( $el_class );
$css_class =  ' ' . $class . $el_class;
?>
<a class="<?php echo $vc->esc_attr( trim( $css_class ) ); ?>" href="<?php echo $a_href; ?>"
   title="<?php echo $vc->esc_attr( $a_title ); ?>" target="<?php echo $a_target; ?>">
	<?php echo $title; ?>
</a>
<?php echo $this->endBlockComment( 'vc_button' ) . "\n";