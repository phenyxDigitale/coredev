<?php

class ComposerSharedLibrary {

	private static $colors = [
		'Blue'        => 'blue', // Why $vc_main->l('Blue') doesn't work?
		'Turquoise'   => 'turquoise',
		'Pink'        => 'pink',
		'Violet'      => 'violet',
		'Peacoc'      => 'peacoc',
		'Chino'       => 'chino',
		'Mulled Wine' => 'mulled_wine',
		'Vista Blue'  => 'vista_blue',
		'Black'       => 'black',
		'Grey'        => 'grey',
		'Orange'      => 'orange',
		'Sky'         => 'sky',
		'Green'       => 'green',
		'Juicy pink'  => 'juicy_pink',
		'Sandy brown' => 'sandy_brown',
		'Purple'      => 'purple',
		'White'       => 'white',
	];

	public static $icons = [
		'Glass'  => 'glass',
		'Music'  => 'music',
		'Search' => 'search',
	];

	public static $sizes = [
		'Mini'   => 'xs',
		'Small'  => 'sm',
		'Normal' => 'md',
		'Large'  => 'lg',
	];

	public static $button_styles = [
		'Rounded'         => 'rounded',
		'Square'          => 'square',
		'Round'           => 'round',
		'Outlined'        => 'outlined',
		'3D'              => '3d',
		'Square Outlined' => 'square_outlined',
	];

	public static $cta_styles = [
		'Rounded'         => 'rounded',
		'Square'          => 'square',
		'Round'           => 'round',
		'Outlined'        => 'outlined',
		'Square Outlined' => 'square_outlined',
	];

	public static $txt_align = [
		'Left'    => 'left',
		'Right'   => 'right',
		'Center'  => 'center',
		'Justify' => 'justify',
	];

	public static $el_widths = [
		'100%' => '',
		'90%'  => '90',
		'80%'  => '80',
		'70%'  => '70',
		'60%'  => '60',
		'50%'  => '50',
	];

	public static $sep_styles = [
		'Border' => '',
		'Dashed' => 'dashed',
		'Dotted' => 'dotted',
		'Double' => 'double',
	];

	public static $box_styles = [
		'Default'              => '',
		'Rounded'              => 'vc_box_rounded',
		'Border'               => 'vc_box_border',
		'Outline'              => 'vc_box_outline',
		'Shadow'               => 'vc_box_shadow',
		'Bordered shadow'      => 'vc_box_shadow_border',
		'3D Shadow'            => 'vc_box_shadow_3d',
		'Circle'               => 'vc_box_circle', //new
		'Circle Border'        => 'vc_box_border_circle', //new
		'Circle Outline'       => 'vc_box_outline_circle', //new
		'Circle Shadow'        => 'vc_box_shadow_circle', //new
		'Circle Border Shadow' => 'vc_box_shadow_border_circle', //new
	];

	public static function getColors() {

		return self::$colors;
	}

	public static function getIcons() {

		return self::$icons;
	}

	public static function getSizes() {

		return self::$sizes;
	}

	public static function getButtonStyles() {

		return self::$button_styles;
	}

	public static function getCtaStyles() {

		return self::$cta_styles;
	}

	public static function getTextAlign() {

		return self::$txt_align;
	}

	public static function getElementWidths() {

		return self::$el_widths;
	}

	public static function getSeparatorStyles() {

		return self::$sep_styles;
	}

	public static function getBoxStyles() {

		return self::$box_styles;
	}

}