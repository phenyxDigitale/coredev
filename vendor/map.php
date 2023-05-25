<?php


$phenyxImgSizesOption = Composer::getPhenyxImgSizesOption();

$vc_main = ephenyx_manager();

$colors_arr = [
	$vc_main->l('Grey')      => 'wpb_button',
	$vc_main->l('Blue')      => 'btn-primary',
	$vc_main->l('Turquoise') => 'btn-info',
	$vc_main->l('Green')     => 'btn-success',
	$vc_main->l('Orange')    => 'btn-warning',
	$vc_main->l('Red')       => 'btn-danger',
	$vc_main->l('Black')     => "btn-inverse",
];

// Used in "Button" and "Call to Action" blocks
$size_arr = [
	$vc_main->l('Regular size') => 'wpb_regularsize',
	$vc_main->l('Large')        => 'btn-large',
	$vc_main->l('Small')        => 'btn-small',
	$vc_main->l('Mini')         => "btn-mini",
];

$target_arr = [
	$vc_main->l('Same window') => '_self',
	$vc_main->l('New window')  => "_blank",
];

$add_css_animation = [
	'type'        => 'dropdown',
	'heading'     => $vc_main->l('CSS Animation'),
	'param_name'  => 'css_animation',
	'admin_label' => true,
	'value'       => [
		$vc_main->l('No')                 => '',
		$vc_main->l('Top to bottom')      => 'top-to-bottom',
		$vc_main->l('Bottom to top')      => 'bottom-to-top',
		$vc_main->l('Left to right')      => 'left-to-right',
		$vc_main->l('Right to left')      => 'right-to-left',
		$vc_main->l('Appear from center') => "appear",
	],
	'description' => $vc_main->l('Select type of animation if you want this element to be animated when it enters into the browsers viewport. Note: Works only in modern browsers.'),
];

$new_css_animation = [
    'type'        => 'animation',
						'heading'     => $vc_main->l('CSS Animation'),
						'param_name'  => 'css_animation',
						'admin_label' => true,
	'value'       => [
            [
				'values' => [
					$vc_main->l('None') => 'none',
				],
			],
			[
				'label'  => $vc_main->l('Attention Seekers'),
				'values' => [
					// text to display => value
					$vc_main->l('bounce')     => [
						'value' => 'bounce',
						'type'  => 'other',
					],
					$vc_main->l('flash')      => [
						'value' => 'flash',
						'type'  => 'other',
					],
					$vc_main->l('pulse')      => [
						'value' => 'pulse',
						'type'  => 'other',
					],
					$vc_main->l('rubberBand') => [
						'value' => 'rubberBand',
						'type'  => 'other',
					],
					$vc_main->l('shake')      => [
						'value' => 'shake',
						'type'  => 'other',
					],
					$vc_main->l('swing')      => [
						'value' => 'swing',
						'type'  => 'other',
					],
					$vc_main->l('tada')       => [
						'value' => 'tada',
						'type'  => 'other',
					],
					$vc_main->l('wobble')     => [
						'value' => 'wobble',
						'type'  => 'other',
					],
				],
			],
			[
				'label'  => $vc_main->l('Bouncing Entrances'),
				'values' => [
					// text to display => value
					$vc_main->l('bounceIn')      => [
						'value' => 'bounceIn',
						'type'  => 'in',
					],
					$vc_main->l('bounceInDown')  => [
						'value' => 'bounceInDown',
						'type'  => 'in',
					],
					$vc_main->l('bounceInLeft')  => [
						'value' => 'bounceInLeft',
						'type'  => 'in',
					],
					$vc_main->l('bounceInRight') => [
						'value' => 'bounceInRight',
						'type'  => 'in',
					],
					$vc_main->l('bounceInUp')    => [
						'value' => 'bounceInUp',
						'type'  => 'in',
					],
				],
			],
			[
				'label'  => $vc_main->l('Bouncing Exits'),
				'values' => [
					// text to display => value
					$vc_main->l('bounceOut')      => [
						'value' => 'bounceOut',
						'type'  => 'out',
					],
					$vc_main->l('bounceOutDown')  => [
						'value' => 'bounceOutDown',
						'type'  => 'out',
					],
					$vc_main->l('bounceOutLeft')  => [
						'value' => 'bounceOutLeft',
						'type'  => 'out',
					],
					$vc_main->l('bounceOutRight') => [
						'value' => 'bounceOutRight',
						'type'  => 'out',
					],
					$vc_main->l('bounceOutUp')    => [
						'value' => 'bounceOutUp',
						'type'  => 'out',
					],
				],
			],
			[
				'label'  => $vc_main->l('Fading Entrances'),
				'values' => [
					// text to display => value
					$vc_main->l('fadeIn')         => [
						'value' => 'fadeIn',
						'type'  => 'in',
					],
					$vc_main->l('fadeInDown')     => [
						'value' => 'fadeInDown',
						'type'  => 'in',
					],
					$vc_main->l('fadeInDownBig')  => [
						'value' => 'fadeInDownBig',
						'type'  => 'in',
					],
					$vc_main->l('fadeInLeft')     => [
						'value' => 'fadeInLeft',
						'type'  => 'in',
					],
					$vc_main->l('fadeInLeftBig')  => [
						'value' => 'fadeInLeftBig',
						'type'  => 'in',
					],
					$vc_main->l('fadeInRight')    => [
						'value' => 'fadeInRight',
						'type'  => 'in',
					],
					$vc_main->l('fadeInRightBig') => [
						'value' => 'fadeInRightBig',
						'type'  => 'in',
					],
					$vc_main->l('fadeInUp')       => [
						'value' => 'fadeInUp',
						'type'  => 'in',
					],
					$vc_main->l('fadeInUpBig')    => [
						'value' => 'fadeInUpBig',
						'type'  => 'in',
					],
				],
			],
			[
				'label'  => $vc_main->l('Fading Exits'),
				'values' => [
					$vc_main->l('fadeOut')         => [
						'value' => 'fadeOut',
						'type'  => 'out',
					],
					$vc_main->l('fadeOutDown')     => [
						'value' => 'fadeOutDown',
						'type'  => 'out',
					],
					$vc_main->l('fadeOutDownBig')  => [
						'value' => 'fadeOutDownBig',
						'type'  => 'out',
					],
					$vc_main->l('fadeOutLeft')     => [
						'value' => 'fadeOutLeft',

						'type'  => 'out',
					],
					$vc_main->l('fadeOutLeftBig')  => [
						'value' => 'fadeOutLeftBig',
						'type'  => 'out',
					],
					$vc_main->l('fadeOutRight')    => [
						'value' => 'fadeOutRight',
						'type'  => 'out',
					],
					$vc_main->l('fadeOutRightBig') => [
						'value' => 'fadeOutRightBig',
						'type'  => 'out',
					],
					$vc_main->l('fadeOutUp')       => [
						'value' => 'fadeOutUp',
						'type'  => 'out',
					],
					$vc_main->l('fadeOutUpBig')    => [
						'value' => 'fadeOutUpBig',
						'type'  => 'out',
					],
				],
			],
			[
				'label'  => $vc_main->l('Flippers'),
				'values' => [
					$vc_main->l('flip')     => [
						'value' => 'flip',
						'type'  => 'other',
					],
					$vc_main->l('flipInX')  => [
						'value' => 'flipInX',
						'type'  => 'in',
					],
					$vc_main->l('flipInY')  => [
						'value' => 'flipInY',
						'type'  => 'in',
					],
					$vc_main->l('flipOutX') => [
						'value' => 'flipOutX',
						'type'  => 'out',
					],
					$vc_main->l('flipOutY') => [
						'value' => 'flipOutY',
						'type'  => 'out',
					],
				],
			],
			[
				'label'  => $vc_main->l('Lightspeed'),
				'values' => [
					$vc_main->l('lightSpeedIn')  => [
						'value' => 'lightSpeedIn',
						'type'  => 'in',
					],
					$vc_main->l('lightSpeedOut') => [
						'value' => 'lightSpeedOut',
						'type'  => 'out',
					],
				],
			],
			[
				'label'  => $vc_main->l('Rotating Entrances'),
				'values' => [
					$vc_main->l('rotateIn')          => [
						'value' => 'rotateIn',
						'type'  => 'in',
					],
					$vc_main->l('rotateInDownLeft')  => [
						'value' => 'rotateInDownLeft',
						'type'  => 'in',
					],
					$vc_main->l('rotateInDownRight') => [
						'value' => 'rotateInDownRight',
						'type'  => 'in',
					],
					$vc_main->l('rotateInUpLeft')    => [
						'value' => 'rotateInUpLeft',
						'type'  => 'in',
					],
					$vc_main->l('rotateInUpRight')   => [
						'value' => 'rotateInUpRight',
						'type'  => 'in',
					],
				],
			],
			[
				'label'  => $vc_main->l('Rotating Exits'),
				'values' => [
					$vc_main->l('rotateOut')          => [
						'value' => 'rotateOut',
						'type'  => 'out',
					],
					$vc_main->l('rotateOutDownLeft')  => [
						'value' => 'rotateOutDownLeft',
						'type'  => 'out',
					],
					$vc_main->l('rotateOutDownRight') => [
						'value' => 'rotateOutDownRight',
						'type'  => 'out',
					],
					$vc_main->l('rotateOutUpLeft')    => [
						'value' => 'rotateOutUpLeft',
						'type'  => 'out',
					],
					$vc_main->l('rotateOutUpRight')   => [
						'value' => 'rotateOutUpRight',
						'type'  => 'out',
					],
				],
			],
			[
				'label'  => $vc_main->l('Specials'),
				'values' => [
					$vc_main->l('hinge')   => [
						'value' => 'hinge',
						'type'  => 'out',
					],
					$vc_main->l('rollIn')  => [
						'value' => 'rollIn',
						'type'  => 'in',
					],
					$vc_main->l('rollOut') => [
						'value' => 'rollOut',
						'type'  => 'out',
					],
				],
			],
			[
				'label'  => $vc_main->l('Zoom Entrances'),
				'values' => [
					$vc_main->l('zoomIn')      => [
						'value' => 'zoomIn',
						'type'  => 'in',
					],
					$vc_main->l('zoomInDown')  => [
						'value' => 'zoomInDown',
						'type'  => 'in',
					],
					$vc_main->l('zoomInLeft')  => [
						'value' => 'zoomInLeft',
						'type'  => 'in',
					],
					$vc_main->l('zoomInRight') => [
						'value' => 'zoomInRight',
						'type'  => 'in',
					],
					$vc_main->l('zoomInUp')    => [
						'value' => 'zoomInUp',
						'type'  => 'in',
					],
				],
			],
			[
				'label'  => $vc_main->l('Zoom Exits'),
				'values' => [
					$vc_main->l('zoomOut')      => [
						'value' => 'zoomOut',
						'type'  => 'out',
					],
					$vc_main->l('zoomOutDown')  => [
						'value' => 'zoomOutDown',
						'type'  => 'out',
					],
					$vc_main->l('zoomOutLeft')  => [
						'value' => 'zoomOutLeft',
						'type'  => 'out',
					],
					$vc_main->l('zoomOutRight') => [
						'value' => 'zoomOutRight',
						'type'  => 'out',
					],
					$vc_main->l('zoomOutUp')    => [
						'value' => 'zoomOutUp',
						'type'  => 'out',
					],
				],
			],
			[
				'label'  => $vc_main->l('Slide Entrances'),
				'values' => [
					$vc_main->l('slideInDown')  => [
						'value' => 'slideInDown',
						'type'  => 'in',
					],
					$vc_main->l('slideInLeft')  => [
						'value' => 'slideInLeft',
						'type'  => 'in',
					],
					$vc_main->l('slideInRight') => [
						'value' => 'slideInRight',
						'type'  => 'in',
					],
					$vc_main->l('slideInUp')    => [
						'value' => 'slideInUp',
						'type'  => 'in',
					],
				],
			],
			[
				'label'  => $vc_main->l('Slide Exits'),
				'values' => [
					$vc_main->l('slideOutDown')  => [
						'value' => 'slideOutDown',
						'type'  => 'out',
					],
					$vc_main->l('slideOutLeft')  => [
						'value' => 'slideOutLeft',
						'type'  => 'out',
					],
					$vc_main->l('slideOutRight') => [
						'value' => 'slideOutRight',
						'type'  => 'out',
					],
					$vc_main->l('slideOutUp')    => [
						'value' => 'slideOutUp',
						'type'  => 'out',
					],
				],
			],
        ],
    'description' => $vc_main->l('Select type of animation if you want this element to be animated when it enters into the browsers viewport. Note: Works only in modern browsers.'),
];
		
map([
	'name'                    => $vc_main->l('Row'),
	'base'                    => 'vc_row',
	'is_container'            => true,
	'icon'                    => 'icon-wpb-row',
	'show_settings_on_create' => false,
	'category'                => $vc_main->l('Content'),
	'description'             => $vc_main->l('Place content elements inside the row'),
	'params'                  => [
		/* New Params */
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Row stretch'),
			'param_name'  => 'full_width',
			'value'       => [
				$vc_main->l('Default')                               => '',
				$vc_main->l('Stretch row')                           => 'stretch_row',
				$vc_main->l('Stretch row and content')               => 'stretch_row_content',
				$vc_main->l('Stretch row and content (no paddings)') => 'stretch_row_content_no_spaces',
			],
			'description' => $vc_main->l('Select stretching options for row and content (Note: stretched may not work properly if parent container has "overflow: hidden" CSS property).'),
		],
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Columns gap'),
			'param_name'  => 'gap',
			'value'       => [
				'0px'  => '0',
				'1px'  => '1',
				'2px'  => '2',
				'3px'  => '3',
				'4px'  => '4',
				'5px'  => '5',
				'10px' => '10',
				'15px' => '15',
				'20px' => '20',
				'25px' => '25',
				'30px' => '30',
				'35px' => '35',
			],
			'std'         => '0',
			'description' => $vc_main->l('Select gap between columns in row.'),
		],
		[
			'type'        => 'checkbox',
			'heading'     => $vc_main->l('Full height row?'),
			'param_name'  => 'full_height',
			'description' => $vc_main->l('If checked row will be set to full height.'),
			'value'       => [$vc_main->l('Yes') => 'yes'],
		],
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Columns position'),
			'param_name'  => 'columns_placement',
			'value'       => [
				$vc_main->l('Middle')  => 'middle',
				$vc_main->l('Top')     => 'top',
				$vc_main->l('Bottom')  => 'bottom',
				$vc_main->l('Stretch') => 'stretch',
			],
			'description' => $vc_main->l('Select columns position within row.'),
			'dependency'  => [
				'element'   => 'full_height',
				'not_empty' => true,
			],
		],
		[
			'type'        => 'checkbox',
			'heading'     => $vc_main->l('Equal height'),
			'param_name'  => 'equal_height',
			'description' => $vc_main->l('If checked columns will be set to equal height.'),
			'value'       => [$vc_main->l('Yes') => 'yes'],
		],
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Content position'),
			'param_name'  => 'content_placement',
			'value'       => [
				$vc_main->l('Default') => '',
				$vc_main->l('Top')     => 'top',
				$vc_main->l('Middle')  => 'middle',
				$vc_main->l('Bottom')  => 'bottom',
			],
			'description' => $vc_main->l('Select content position within columns.'),
		],
		[
			'type'        => 'checkbox',
			'heading'     => $vc_main->l('Use video background?'),
			'param_name'  => 'video_bg',
			'description' => $vc_main->l('If checked, video will be used as row background.'),
			'value'       => [$vc_main->l('Yes') => 'yes'],
		],
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('YouTube link'),
			'param_name'  => 'video_bg_url',
			'value'       => '',
			// default video url
			'description' => $vc_main->l('Add YouTube link.'),
			'dependency'  => [
				'element'   => 'video_bg',
				'not_empty' => true,
			],
		],
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Parallax'),
			'param_name'  => 'video_bg_parallax',
			'value'       => [
				$vc_main->l('None')      => '',
				$vc_main->l('Simple')    => 'content-moving',
				$vc_main->l('With fade') => 'content-moving-fade',
			],
			'description' => $vc_main->l('Add parallax type background for row.'),
			'dependency'  => [
				'element'   => 'video_bg',
				'not_empty' => true,
			],
		],
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Parallax'),
			'param_name'  => 'parallax',
			'value'       => [
				$vc_main->l('None')      => '',
				$vc_main->l('Simple')    => 'content-moving',
				$vc_main->l('With fade') => 'content-moving-fade',
			],
			'description' => $vc_main->l('Add parallax type background for row (Note: If no image is specified, parallax will use background image from Design Options).'),
			'dependency'  => [
				'element'  => 'video_bg',
				'is_empty' => true,
			],
		],
		[
			'type'        => 'attach_image',
			'heading'     => $vc_main->l('Image'),
			'param_name'  => 'parallax_image',
			'value'       => '',
			'description' => $vc_main->l('Select image from media library.'),
			'dependency'  => [
				'element'   => 'parallax',
				'not_empty' => true,
			],
		],
		[
			'type'        => 'el_id',
			'heading'     => $vc_main->l('Row ID'),
			'param_name'  => 'el_id',
			'description' => sprintf($vc_main->l('Enter row ID (Note: make sure it is unique and valid according to <a href="%s" target="_blank">w3c specification</a>).'), 'http://www.w3schools.com/tags/att_global_id.asp'),
		],
		/* New params end */
		[
			'type'             => 'colorpicker',
			'heading'          => $vc_main->l('Font Color'),
			'param_name'       => 'font_color',
			'description'      => $vc_main->l('Select font color'),
			'edit_field_class' => 'vc_col-md-6 vc_column',
		],
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Extra class name'),
			'param_name'  => 'el_class',
			'description' => $vc_main->l('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.'),
		],
		[
			'type'       => 'css_editor',
			'heading'    => $vc_main->l('Css'),
			'param_name' => 'css',
			// 'description' => $vc_main->l('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.'),
			'group'      => $vc_main->l('Design options'),
		],
	],
	'js_view'                 => 'VcRowView',
]);
map([
	'name'                    => $vc_main->l('Row'), //Inner Row
	'base'                    => 'vc_row_inner',
	'content_element'         => false,
	'is_container'            => true,
	'icon'                    => 'icon-wpb-row',
	'weight'                  => 1000,
	'show_settings_on_create' => false,
	'description'             => $vc_main->l('Place content elements inside the row'),
	'params'                  => [
		[
			'type'             => 'colorpicker',
			'heading'          => $vc_main->l('Font Color'),
			'param_name'       => 'font_color',
			'description'      => $vc_main->l('Select font color'),
			'edit_field_class' => 'vc_col-md-6 vc_column',
		],
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Extra class name'),
			'param_name'  => 'el_class',
			'description' => $vc_main->l('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.'),
		],
		[
			'type'       => 'css_editor',
			'heading'    => $vc_main->l('Css'),
			'param_name' => 'css',
			// 'description' => $vc_main->l('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.'),
			'group'      => $vc_main->l('Design options'),
		],
	],
	'js_view'                 => 'VcRowView',
]);
$column_width_list = [
	$vc_main->l('1 column - 1/12')    => '1/12',
	$vc_main->l('2 columns - 1/6')    => '1/6',
	$vc_main->l('3 columns - 1/4')    => '1/4',
	$vc_main->l('4 columns - 1/3')    => '1/3',
	$vc_main->l('5 columns - 5/12')   => '5/12',
	$vc_main->l('6 columns - 1/2')    => '1/2',
	$vc_main->l('7 columns - 7/12')   => '7/12',
	$vc_main->l('8 columns - 2/3')    => '2/3',
	$vc_main->l('9 columns - 3/4')    => '3/4',
	$vc_main->l('10 columns - 5/6')   => '5/6',
	$vc_main->l('11 columns - 11/12') => '11/12',
	$vc_main->l('12 columns - 1/1')   => '1/1',
];
map([
	'name'            => $vc_main->l('Column'),
	'base'            => 'vc_column',
	'is_container'    => true,
	'content_element' => false,
	'params'          => [
		[
			'type'             => 'colorpicker',
			'heading'          => $vc_main->l('Font Color'),
			'param_name'       => 'font_color',
			'description'      => $vc_main->l('Select font color'),
			'edit_field_class' => 'vc_col-md-6 vc_column',
		],
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Extra class name'),
			'param_name'  => 'el_class',
			'description' => $vc_main->l('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.'),
		],
		[
			'type'       => 'css_editor',
			'heading'    => $vc_main->l('Css'),
			'param_name' => 'css',
			// 'description' => $vc_main->l('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.'),
			'group'      => $vc_main->l('Design options'),
		],
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Width'),
			'param_name'  => 'width',
			'value'       => $column_width_list,
			'group'       => $vc_main->l('Width & Responsiveness'),
			'description' => $vc_main->l('Select column width.'),
			'std'         => '1/1',
		],
		[
			'type'        => 'column_offset',
			'heading'     => $vc_main->l('Responsiveness'),
			'param_name'  => 'offset',
			'group'       => $vc_main->l('Width & Responsiveness'),
			'description' => $vc_main->l('Adjust column for different screen sizes. Control width, offset and visibility settings.'),
		],
	],
	'js_view'         => 'VcColumnView',
]);

map([
	"name"                      => $vc_main->l("Column"),
	"base"                      => "vc_column_inner",
	"class"                     => "",
	"icon"                      => "",
	"wrapper_class"             => "",
	"controls"                  => "full",
	"allowed_container_element" => false,
	"content_element"           => false,
	"is_container"              => true,
	"params"                    => [
		[
			'type'             => 'colorpicker',
			'heading'          => $vc_main->l('Font Color'),
			'param_name'       => 'font_color',
			'description'      => $vc_main->l('Select font color'),
			'edit_field_class' => 'vc_col-md-6 vc_column',
		],
		[
			"type"        => "textfield",
			"heading"     => $vc_main->l("Extra class name"),
			"param_name"  => "el_class",
			"value"       => "",
			"description" => $vc_main->l("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file."),
		],
		[
			"type"       => "css_editor",
			"heading"    => $vc_main->l('Css'),
			"param_name" => "css",
			// "description" => $vc_main->l("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", "js_composer"),
			"group"      => $vc_main->l('Design options'),
		],
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Width'),
			'param_name'  => 'width',
			'value'       => $column_width_list,
			'group'       => $vc_main->l('Width & Responsiveness'),
			'description' => $vc_main->l('Select column width.'),
			'std'         => '1/1',
		],
	],
	"js_view"                   => 'VcColumnView',
]);
/* Text Block
---------------------------------------------------------- */
map([
	'name'          => $vc_main->l('Text Block'),
	'base'          => 'vc_column_text',
	'icon'          => 'icon-wpb-layer-shape-text',
	'wrapper_class' => 'clearfix',
	'category'      => $vc_main->l('Content'),
	'description'   => $vc_main->l('A block of text with WYSIWYG editor'),
	'params'        => [
		[
			'type'       => 'textarea_html',
			'holder'     => 'div',
			'heading'    => $vc_main->l('Text'),
			'param_name' => 'content',
			'value'      => $vc_main->l('<p>I am text block. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.</p>'),
		],
		$new_css_animation,
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Extra class name'),
			'param_name'  => 'el_class',
			'description' => $vc_main->l('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.'),
		],
		[
			'type'       => 'css_editor',
			'heading'    => $vc_main->l('Css'),
			'param_name' => 'css',
			// 'description' => $vc_main->l('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.'),
			'group'      => $vc_main->l('Design options'),
		],
	],
]);

map([
	'name'                    => $vc_main->l('Separator'),
	'base'                    => 'vc_separator',
	'icon'                    => 'icon-wpb-ui-separator',
	'show_settings_on_create' => true,
	'category'                => $vc_main->l('Content'),
	'description'             => $vc_main->l('Horizontal separator line'),
	'params'                  => [
		[
			'type'               => 'dropdown',
			'heading'            => $vc_main->l('Color'),
			'param_name'         => 'color',
			'value'              => array_merge(getComposerShared('colors'), [$vc_main->l('Custom color') => 'custom']),
			'std'                => 'grey',
			'description'        => $vc_main->l('Separator color.'),
			'param_holder_class' => 'vc_colored-dropdown',
		],
		[
			'type'        => 'colorpicker',
			'heading'     => $vc_main->l('Custom Border Color'),
			'param_name'  => 'accent_color',
			'description' => $vc_main->l('Select border color for your element.'),
			'dependency'  => [
				'element' => 'color',
				'value'   => ['custom'],
			],
		],
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Style'),
			'param_name'  => 'style',
			'value'       => getComposerShared('separator styles'),
			'description' => $vc_main->l('Separator style.'),
		],
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Element width'),
			'param_name'  => 'el_width',
			'value'       => getComposerShared('separator widths'),
			'description' => $vc_main->l('Separator element width in percents.'),
		],
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Extra class name'),
			'param_name'  => 'el_class',
			'description' => $vc_main->l('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.'),
		],
	],
]);

/* Textual block
---------------------------------------------------------- */
map([
	'name'        => $vc_main->l('Separator with Text'),
	'base'        => 'vc_text_separator',
	'icon'        => 'icon-wpb-ui-separator-label',
	'category'    => $vc_main->l('Content'),
	'description' => $vc_main->l('Horizontal separator line with heading'),
	'params'      => [
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Title'),
			'param_name'  => 'title',
			'holder'      => 'div',
			'value'       => $vc_main->l('Title'),
			'description' => $vc_main->l('Separator title.'),
		],
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Title position'),
			'param_name'  => 'title_align',
			'value'       => [
				$vc_main->l('Align center') => 'separator_align_center',
				$vc_main->l('Align left')   => 'separator_align_left',
				$vc_main->l('Align right')  => "separator_align_right",
			],
			'description' => $vc_main->l('Select title location.'),
		],
		[
			'type'               => 'dropdown',
			'heading'            => $vc_main->l('Color'),
			'param_name'         => 'color',
			'value'              => array_merge(getComposerShared('colors'), [$vc_main->l('Custom color') => 'custom']),
			'std'                => 'grey',
			'description'        => $vc_main->l('Separator color.'),
			'param_holder_class' => 'vc_colored-dropdown',
		],
		[
			'type'        => 'colorpicker',
			'heading'     => $vc_main->l('Custom Color'),
			'param_name'  => 'accent_color',
			'description' => $vc_main->l('Custom separator color for your element.'),
			'dependency'  => [
				'element' => 'color',
				'value'   => ['custom'],
			],
		],
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Style'),
			'param_name'  => 'style',
			'value'       => getComposerShared('separator styles'),
			'description' => $vc_main->l('Separator style.'),
		],
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Element width'),
			'param_name'  => 'el_width',
			'value'       => getComposerShared('separator widths'),
			'description' => $vc_main->l('Separator element width in percents.'),
		],
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Extra class name'),
			'param_name'  => 'el_class',
			'description' => $vc_main->l('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.'),
		],
	],
	'js_view'     => 'VcTextSeparatorView',
]);

/* Message box
---------------------------------------------------------- */
map([
	'name'          => $vc_main->l('Message Box'),
	'base'          => 'vc_message',
	'icon'          => 'icon-wpb-information-white',
	'wrapper_class' => 'alert',
	'category'      => $vc_main->l('Content'),
	'description'   => $vc_main->l('Notification box'),
	'params'        => [
		[
			'type'               => 'dropdown',
			'heading'            => $vc_main->l('Message box type'),
			'param_name'         => 'color',
			'value'              => [
				$vc_main->l('Informational') => 'alert-info',
				$vc_main->l('Warning')       => 'alert-warning',
				$vc_main->l('Success')       => 'alert-success',
				$vc_main->l('Error')         => "alert-danger",
			],
			'description'        => $vc_main->l('Select message type.'),
			'param_holder_class' => 'vc_message-type',
		],
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Style'),
			'param_name'  => 'style',
			'value'       => getComposerShared('alert styles'),
			'description' => $vc_main->l('Alert style.'),
		],
		[
			'type'       => 'textarea_html',
			'holder'     => 'div',
			'class'      => 'messagebox_text',
			'heading'    => $vc_main->l('Message text'),
			'param_name' => 'content',
			'value'      => $vc_main->l('<p>I am message box. Click edit button to change this text.</p>'),
		],
		$add_css_animation,
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Extra class name'),
			'param_name'  => 'el_class',
			'description' => $vc_main->l('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.'),
		],
	],
	'js_view'       => 'VcMessageView',
]);

/* Facebook like button
---------------------------------------------------------- */
map([
	'name'        => $vc_main->l('Facebook Like'),
	'base'        => 'vc_facebook',
	'icon'        => 'icon-wpb-balloon-facebook-left',
	'category'    => $vc_main->l('Social'),
	'description' => $vc_main->l('Facebook like button'),
	'params'      => [
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Button type'),
			'param_name'  => 'type',
			'admin_label' => true,
			'value'       => [
				$vc_main->l('Standard')     => 'standard',
				$vc_main->l('Button count') => 'button_count',
				$vc_main->l('Box count')    => 'box_count',
			],
			'description' => $vc_main->l('Select button type.'),
		],
	],
]);

/* Tweetmeme button
---------------------------------------------------------- */
map([
	'name'        => $vc_main->l('Tweetmeme Button'),
	'base'        => 'vc_tweetmeme',
	'icon'        => 'icon-wpb-tweetme',
	'category'    => $vc_main->l('Social'),
	'description' => $vc_main->l('Share on twitter button'),
	'params'      => [
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Button type'),
			'param_name'  => 'type',
			'admin_label' => true,
			'value'       => [
				$vc_main->l('Horizontal') => 'horizontal',
				$vc_main->l('Vertical')   => 'vertical',
				$vc_main->l('None')       => 'none',
			],
			'description' => $vc_main->l('Select button type.'),
		],
	],
]);

/* Google+ button
---------------------------------------------------------- */
map([
	'name'        => $vc_main->l('Google+ Button'),
	'base'        => 'vc_googleplus',
	'icon'        => 'icon-wpb-application-plus',
	'category'    => $vc_main->l('Social'),
	'description' => $vc_main->l('Recommend on Google'),
	'params'      => [
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Button size'),
			'param_name'  => 'type',
			'admin_label' => true,
			'value'       => [
				$vc_main->l('Standard') => '',
				$vc_main->l('Small')    => 'small',
				$vc_main->l('Medium')   => 'medium',
				$vc_main->l('Tall')     => 'tall',
			],
			'description' => $vc_main->l('Select button size.'),
		],
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Annotation'),
			'param_name'  => 'annotation',
			'admin_label' => true,
			'value'       => [
				$vc_main->l('Inline') => 'inline',
				$vc_main->l('Bubble') => '',
				$vc_main->l('None')   => 'none',
			],
			'description' => $vc_main->l('Select type of annotation'),
		],
	],
]);

/* Pinterest button
---------------------------------------------------------- */
map([
	'name'        => $vc_main->l('Pinterest'),
	'base'        => 'vc_pinterest',
	'icon'        => 'icon-wpb-pinterest',
	'category'    => $vc_main->l('Social'),
	'description' => $vc_main->l('Pinterest button'),
	"params"      => [
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Button layout'),
			'param_name'  => 'type',
			'admin_label' => true,
			'value'       => [
				$vc_main->l('Horizontal') => '',
				$vc_main->l('Vertical')   => 'vertical',
				$vc_main->l('No count')   => 'none'],
			'description' => $vc_main->l('Select button layout.'),
		],
	],
]);

/* Toggle (FAQ)
---------------------------------------------------------- */
map([
	'name'        => $vc_main->l('FAQ'),
	'base'        => 'vc_toggle',
	'icon'        => 'icon-wpb-toggle-small-expand',
	'category'    => $vc_main->l('Content'),
	'description' => $vc_main->l('Toggle element for Q&A block'),
	'params'      => [
		[
			'type'        => 'textfield',
			'holder'      => 'h4',
			'class'       => 'toggle_title',
			'heading'     => $vc_main->l('Toggle title'),
			'param_name'  => 'title',
			'value'       => $vc_main->l('Toggle title'),
			'description' => $vc_main->l('Toggle block title.'),
		],
		[
			'type'        => 'textarea_html',
			'holder'      => 'div',
			'class'       => 'toggle_content',
			'heading'     => $vc_main->l('Toggle content'),
			'param_name'  => 'content',
			'value'       => $vc_main->l('<p>Toggle content goes here, click edit button to change this text.</p>'),
			'description' => $vc_main->l('Toggle block content.'),
		],
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Default state'),
			'param_name'  => 'open',
			'value'       => [
				$vc_main->l('Closed') => 'false',
				$vc_main->l('Open')   => 'true',
			],
			'description' => $vc_main->l('Select "Open" if you want toggle to be open by default.'),
		],
		$add_css_animation,
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Extra class name'),
			'param_name'  => 'el_class',
			'description' => $vc_main->l('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.'),
		],
	],
	'js_view'     => 'VcToggleView',
]);

/* Single image */
map([
	'name'        => $vc_main->l('Single Image'),
	'base'        => 'vc_single_image',
	'icon'        => 'icon-wpb-single-image',
	'category'    => $vc_main->l('Content'),
	'description' => $vc_main->l('Simple image with CSS animation'),
	'params'      => [
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Widget title'),
			'param_name'  => 'title',
			'description' => $vc_main->l('Enter text which will be used as widget title. Leave blank if no title is needed.'),
		],
		[
			'type'        => 'attach_image',
			'heading'     => $vc_main->l('Image'),
			'param_name'  => 'image',
			'value'       => '',
			'description' => $vc_main->l('Select image from media library.'),
		],
		$add_css_animation,
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Image size'),
			'param_name'  => 'img_size',
			'description' => $vc_main->l('Enter image size. Example: ' . vc_get_image_sizes_string() . '. Leave empty to use main image.'),
		],
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Image alignment'),
			'param_name'  => 'alignment',
			'value'       => [
				$vc_main->l('Align left')   => '',
				$vc_main->l('Align right')  => 'right',
				$vc_main->l('Align center') => 'center',
			],
			'description' => $vc_main->l('Select image alignment.'),
		],
		[
			'type'       => 'dropdown',
			'heading'    => $vc_main->l('Image style'),
			'param_name' => 'style',
			'value'      => getComposerShared('single image styles'),
		],
		[
			'type'               => 'dropdown',
			'heading'            => $vc_main->l('Border color'),
			'param_name'         => 'border_color',
			'value'              => getComposerShared('colors'),
			'std'                => 'grey',
			'dependency'         => [
				'element' => 'style',
				'value'   => ['vc_box_border', 'vc_box_border_circle', 'vc_box_outline', 'vc_box_outline_circle'],
			],
			'description'        => $vc_main->l('Border color.'),
			'param_holder_class' => 'vc_colored-dropdown',
		],
		[
			'type'        => 'checkbox',
			'heading'     => $vc_main->l('Link to large image?'),
			'param_name'  => 'img_link_large',
			'description' => $vc_main->l('If selected, image will be linked to the larger image.'),
			'value'       => [$vc_main->l('Yes, please') => 'yes'],
		],
		[
			'type'        => 'href',
			'heading'     => $vc_main->l('Image link'),
			'param_name'  => 'link',
			'description' => $vc_main->l('Enter URL if you want this image to have a link.'),
			'dependency'  => [
				'element'  => 'img_link_large',
				'is_empty' => true,
				'callback' => 'wpb_single_image_img_link_dependency_callback',
			],
		],
		[
			'type'       => 'dropdown',
			'heading'    => $vc_main->l('Link Target'),
			'param_name' => 'img_link_target',
			'value'      => $target_arr,
			'dependency' => [
				'element'   => 'img_link',
				'not_empty' => true,
			],
		],
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Extra class name'),
			'param_name'  => 'el_class',
			'description' => $vc_main->l('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.'),
		],
		[
			'type'       => 'css_editor',
			'heading'    => $vc_main->l('Css'),
			'param_name' => 'css',
			// 'description' => $vc_main->l('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.'),
			'group'      => $vc_main->l('Design options'),
		],
	],
]);

/* Gallery/Slideshow
---------------------------------------------------------- */
map([
	'name'        => $vc_main->l('Image Gallery'),
	'base'        => 'vc_gallery',
	'icon'        => 'icon-wpb-images-stack',
	'category'    => $vc_main->l('Content'),
	'description' => $vc_main->l('Responsive image gallery'),
	'params'      => [
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Widget title'),
			'param_name'  => 'title',
			'description' => $vc_main->l('Enter text which will be used as widget title. Leave blank if no title is needed.'),
		],
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Gallery type'),
			'param_name'  => 'type',
			'value'       => [
				$vc_main->l('Flex slider fade')  => 'flexslider_fade',
				$vc_main->l('Flex slider slide') => 'flexslider_slide',
				$vc_main->l('Nivo slider')       => 'nivo',
				$vc_main->l('Image grid')        => 'image_grid',
			],
			'description' => $vc_main->l('Select gallery type.'),
		],
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Auto rotate slides'),
			'param_name'  => 'interval',
			'value'       => [3, 5, 10, 15, $vc_main->l('Disable') => 0],
			'description' => $vc_main->l('Auto rotate slides each X seconds.'),
			'dependency'  => [
				'element' => 'type',
				'value'   => ['flexslider_fade', 'flexslider_slide', 'nivo'],
			],
		],
		[
			'type'        => 'attach_images',
			'heading'     => $vc_main->l('Images'),
			'param_name'  => 'images',
			'value'       => '',
			'description' => $vc_main->l('Select images from media library.'),
		],
//		array(
		//			'type' => 'textfield',
		//			'heading' => $vc_main->l('Image size'),
		//			'param_name' => 'img_size',
		//			'description' => $vc_main->l('Enter image size. Example: thumbnail, medium, large, full or other sizes defined by current theme. Alternatively enter image size in pixels: 200x100 (Width x Height). Leave empty to use "thumbnail" size.')
		//		),
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Image size'),
			'param_name'  => 'img_size',
			'value'       => $vc_main->image_sizes_dropdown,
			'description' => $vc_main->l('Enter image size.'),
		],
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('On click'),
			'param_name'  => 'eventclick',
			'value'       => [
				$vc_main->l('Open prettyPhoto') => 'link_image',
				$vc_main->l('Do nothing')       => 'link_no',
				$vc_main->l('Open custom link') => 'custom_link',
			],
			'description' => $vc_main->l('Define action for onclick event if needed.'),
		],
		[
			'type'        => 'exploded_textarea',
			'heading'     => $vc_main->l('Custom links'),
			'param_name'  => 'custom_links',
			'description' => $vc_main->l('Enter links for each slide here. Divide links with linebreaks or comma (Enter) or (,) . '),
			'dependency'  => [
				'element' => 'onclick',
				'value'   => ['custom_link'],
			],
		],
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Custom link target'),
			'param_name'  => 'custom_links_target',
			'description' => $vc_main->l('Select where to open  custom links.'),
			'dependency'  => [
				'element' => 'onclick',
				'value'   => ['custom_link'],
			],
			'value'       => $target_arr,
		],
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Extra class name'),
			'param_name'  => 'el_class',
			'description' => $vc_main->l('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.'),
		],
	],
]);

/* Image Carousel
---------------------------------------------------------- */
map([
	'name'        => $vc_main->l('Image Carousel'),
	'base'        => 'vc_images_carousel',
	'icon'        => 'icon-wpb-images-carousel',
	'category'    => $vc_main->l('Content'),
	'description' => $vc_main->l('Animated carousel with images'),
	'params'      => [
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Widget title'),
			'param_name'  => 'title',
			'description' => $vc_main->l('Enter text which will be used as widget title. Leave blank if no title is needed.'),
		],
		[
			'type'        => 'attach_images',
			'heading'     => $vc_main->l('Images'),
			'param_name'  => 'images',
			'value'       => '',
			'description' => $vc_main->l('Select images from media library.'),
		],
//		array(
		//			'type' => 'textfield',
		//			'heading' => $vc_main->l('Image size'),
		//			'param_name' => 'img_size',
		//			'description' => $vc_main->l('Enter image size. Example: thumbnail, medium, large, full or other sizes defined by current theme. Alternatively enter image size in pixels: 200x100 (Width x Height). Leave empty to use "thumbnail" size.')
		//		),
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Image size'),
			'param_name'  => 'img_size',
			'value'       => $vc_main->image_sizes_dropdown,
			'description' => $vc_main->l('Enter image size.'),
		],
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('On click'),
			'param_name'  => 'eventclick',
			'value'       => [
				$vc_main->l('Open prettyPhoto') => 'link_image',
				$vc_main->l('Do nothing')       => 'link_no',
				$vc_main->l('Open custom link') => 'custom_link',
			],
			'description' => $vc_main->l('What to do when slide is clicked?'),
		],
		[
			'type'        => 'exploded_textarea',
			'heading'     => $vc_main->l('Custom links'),
			'param_name'  => 'custom_links',
			'description' => $vc_main->l('Enter links for each slide here. Divide links with linebreaks or comma (Enter) or (,) . '),
			'dependency'  => [
				'element' => 'onclick',
				'value'   => ['custom_link'],
			],
		],
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Custom link target'),
			'param_name'  => 'custom_links_target',
			'description' => $vc_main->l('Select where to open  custom links.'),
			'dependency'  => [
				'element' => 'onclick',
				'value'   => ['custom_link'],
			],
			'value'       => $target_arr,
		],
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Slider mode'),
			'param_name'  => 'mode',
			'value'       => [
				$vc_main->l('Horizontal') => 'horizontal',
				$vc_main->l('Vertical')   => 'vertical',
			],
			'description' => $vc_main->l('Slides will be positioned horizontally (for horizontal swipes) or vertically (for vertical swipes)'),
		],
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Slider speed'),
			'param_name'  => 'speed',
			'value'       => '5000',
			'description' => $vc_main->l('Duration of animation between slides (in ms)'),
		],
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Slides per view'),
			'param_name'  => 'slides_per_view',
			'value'       => '1',
			'description' => $vc_main->l('Set numbers of slides you want to display at the same time on slider\'s container for carousel mode. Supports also "auto" value, in this case it will fit slides depending on container\'s width. "auto" mode isn\'t compatible with loop mode.'),
		],
		[
			'type'        => 'checkbox',
			'heading'     => $vc_main->l('Slider autoplay'),
			'param_name'  => 'autoplay',
			'description' => $vc_main->l('Enables autoplay mode.'),
			'value'       => [$vc_main->l('Yes, please') => 'yes'],
		],
		[
			'type'        => 'checkbox',
			'heading'     => $vc_main->l('Hide pagination control'),
			'param_name'  => 'hide_pagination_control',
			'description' => $vc_main->l('If YES pagination control will be removed.'),
			'value'       => [$vc_main->l('Yes, please') => 'yes'],
		],
		[
			'type'        => 'checkbox',
			'heading'     => $vc_main->l('Hide prev/next buttons'),
			'param_name'  => 'hide_prev_next_buttons',
			'description' => $vc_main->l('If "YES" prev/next control will be removed.'),
			'value'       => [$vc_main->l('Yes, please') => 'yes'],
		],
		[
			'type'        => 'checkbox',
			'heading'     => $vc_main->l('Partial view'),
			'param_name'  => 'partial_view',
			'description' => $vc_main->l('If "YES" part of the next slide will be visible on the right side.'),
			'value'       => [$vc_main->l('Yes, please') => 'yes'],
		],
		[
			'type'        => 'checkbox',
			'heading'     => $vc_main->l('Slider loop'),
			'param_name'  => 'wrap',
			'description' => $vc_main->l('Enables loop mode.'),
			'value'       => [$vc_main->l('Yes, please') => 'yes'],
		],
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Extra class name'),
			'param_name'  => 'el_class',
			'description' => $vc_main->l('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.'),
		],
	],
]);

/* Tabs
---------------------------------------------------------- */
$tab_id_1 = time() . '-1-' . rand(0, 100);
$tab_id_2 = time() . '-2-' . rand(0, 100);
map([
	"name"                    => $vc_main->l('Tabs'),
	'base'                    => 'vc_tabs',
	'show_settings_on_create' => false,
	'is_container'            => true,
	'icon'                    => 'icon-wpb-ui-tab-content',
	'category'                => $vc_main->l('Content'),
	'description'             => $vc_main->l('Tabbed content'),
	'params'                  => [
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Widget title'),
			'param_name'  => 'title',
			'description' => $vc_main->l('Enter text which will be used as widget title. Leave blank if no title is needed.'),
		],
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Auto rotate tabs'),
			'param_name'  => 'interval',
			'value'       => [$vc_main->l('Disable') => 0, 3, 5, 10, 15],
			'std'         => 0,
			'description' => $vc_main->l('Auto rotate tabs each X seconds.'),
		],
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Extra class name'),
			'param_name'  => 'el_class',
			'description' => $vc_main->l('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.'),
		],
	],
	'custom_markup'           => '
<div class="wpb_tabs_holder wpb_holder vc_container_for_children">
<ul class="tabs_controls">
</ul>
%content%
</div>'
	,
	'default_content'         => '
[vc_tab title="' . $vc_main->l('Tab 1') . '" tab_id="' . $tab_id_1 . '"][/vc_tab]
[vc_tab title="' . $vc_main->l('Tab 2') . '" tab_id="' . $tab_id_2 . '"][/vc_tab]
',
	'js_view'                 => 'VcTabsView',
//	'js_view' => $vc_is_wp_version_3_6_more ? 'VcTabsView' : 'VcTabsView35'
]);

/* Tour section
---------------------------------------------------------- */
$tab_id_1 = time() . '-1-' . rand(0, 100);
$tab_id_2 = time() . '-2-' . rand(0, 100);
ComposerMap::map('vc_tour', [
	'name'                    => $vc_main->l('Tour'),
	'base'                    => 'vc_tour',
	'show_settings_on_create' => false,
	'is_container'            => true,
	'container_not_allowed'   => true,
	'icon'                    => 'icon-wpb-ui-tab-content-vertical',
	'category'                => $vc_main->l('Content'),
	'wrapper_class'           => 'vc_clearfix',
	'description'             => $vc_main->l('Vertical tabbed content'),
	'params'                  => [
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Widget title'),
			'param_name'  => 'title',
			'description' => $vc_main->l('Enter text which will be used as widget title. Leave blank if no title is needed.'),
		],
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Auto rotate slides'),
			'param_name'  => 'interval',
			'value'       => [$vc_main->l('Disable') => 0, 3, 5, 10, 15],
			'std'         => 0,
			'description' => $vc_main->l('Auto rotate slides each X seconds.'),
		],
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Extra class name'),
			'param_name'  => 'el_class',
			'description' => $vc_main->l('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.'),
		],
	],
	'custom_markup'           => '
<div class="wpb_tabs_holder wpb_holder vc_clearfix vc_container_for_children">
<ul class="tabs_controls">
</ul>
%content%
</div>'
	,
	'default_content'         => '
[vc_tab title="' . $vc_main->l('Tab 1') . '" tab_id="' . $tab_id_1 . '"][/vc_tab]
[vc_tab title="' . $vc_main->l('Tab 2') . '" tab_id="' . $tab_id_2 . '"][/vc_tab]
',
	'js_view'                 => 'VcTabsView',
//	'js_view' => $vc_is_wp_version_3_6_more ? 'VcTabsView' : 'VcTabsView35'
]);

map([
	'name'                      => $vc_main->l('Tab'),
	'base'                      => 'vc_tab',
	'allowed_container_element' => 'vc_row',
	'is_container'              => true,
	'content_element'           => false,
	'params'                    => [
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Title'),
			'param_name'  => 'title',
			'description' => $vc_main->l('Tab title.'),
		],
		[
			'type'       => 'tab_id',
			'heading'    => $vc_main->l('Tab ID'),
			'param_name' => "tab_id",
		],
	],
	'js_view'                   => 'VcTabView',
//	'js_view' => $vc_is_wp_version_3_6_more ? 'VcTabView' : 'VcTabView35'
]);

/* Accordion block
---------------------------------------------------------- */
map([
	'name'                    => $vc_main->l('Accordion'),
	'base'                    => 'vc_accordion',
	'show_settings_on_create' => false,
	'is_container'            => true,
	'icon'                    => 'icon-wpb-ui-accordion',
	'category'                => $vc_main->l('Content'),
	'description'             => $vc_main->l('Collapsible content panels'),
	'params'                  => [
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Widget title'),
			'param_name'  => 'title',
			'description' => $vc_main->l('Enter text which will be used as widget title. Leave blank if no title is needed.'),
		],
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Active section'),
			'param_name'  => 'active_tab',
			'description' => $vc_main->l('Enter section number to be active on load or enter false to collapse all sections.'),
		],
		[
			'type'        => 'checkbox',
			'heading'     => $vc_main->l('Allow collapsible all'),
			'param_name'  => 'collapsible',
			'description' => $vc_main->l('Select checkbox to allow all sections to be collapsible.'),
			'value'       => [$vc_main->l('Allow') => 'yes'],
		],
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Extra class name'),
			'param_name'  => 'el_class',
			'description' => $vc_main->l('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.'),
		],
	],
	'custom_markup'           => '
<div class="wpb_accordion_holder wpb_holder clearfix vc_container_for_children">
%content%
</div>
<div class="tab_controls ui-accordion-header">
    <a class="add_tab" title="' . $vc_main->l('Add section') . '"><span class="vc_icon ui-icon-triangle-1-e"></span> <span class="tab-label">' . $vc_main->l('Add section') . '</span></a>
</div>
',
	'default_content'         => '
    [vc_accordion_tab title="' . $vc_main->l('Section 1') . '"][/vc_accordion_tab]
    [vc_accordion_tab title="' . $vc_main->l('Section 2') . '"][/vc_accordion_tab]
',
	'js_view'                 => 'VcAccordionView',
]);
map([
	'name'                      => $vc_main->l('Section'),
	'base'                      => 'vc_accordion_tab',
	'allowed_container_element' => 'vc_row',
	'is_container'              => true,
	'content_element'           => false,
	'params'                    => [
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Title'),
			'param_name'  => 'title',
			'description' => $vc_main->l('Accordion section title.'),
		],
	],
	'js_view'                   => 'VcAccordionTabView',
]);


/* Posts Grid
---------------------------------------------------------- */
$vc_layout_sub_controls = [
	['link_post', $vc_main->l('Link to post')],
	['no_link', $vc_main->l('No link')],
	['link_image', $vc_main->l('Link to bigger image')],
];


/* Button
---------------------------------------------------------- */
$icons_arr = [
	$vc_main->l('None')                     => 'none',
	$vc_main->l('Address book icon')        => 'wpb_address_book',
	$vc_main->l('Alarm clock icon')         => 'wpb_alarm_clock',
	$vc_main->l('Anchor icon')              => 'wpb_anchor',
	$vc_main->l('Application Image icon')   => 'wpb_application_image',
	$vc_main->l('Arrow icon')               => 'wpb_arrow',
	$vc_main->l('Asterisk icon')            => 'wpb_asterisk',
	$vc_main->l('Hammer icon')              => 'wpb_hammer',
	$vc_main->l('Balloon icon')             => 'wpb_balloon',
	$vc_main->l('Balloon Buzz icon')        => 'wpb_balloon_buzz',
	$vc_main->l('Balloon Facebook icon')    => 'wpb_balloon_facebook',
	$vc_main->l('Balloon Twitter icon')     => 'wpb_balloon_twitter',
	$vc_main->l('Battery icon')             => 'wpb_battery',
	$vc_main->l('Binocular icon')           => 'wpb_binocular',
	$vc_main->l('Document Excel icon')      => 'wpb_document_excel',
	$vc_main->l('Document Image icon')      => 'wpb_document_image',
	$vc_main->l('Document Music icon')      => 'wpb_document_music',
	$vc_main->l('Document Office icon')     => 'wpb_document_office',
	$vc_main->l('Document PDF icon')        => 'wpb_document_pdf',
	$vc_main->l('Document Powerpoint icon') => 'wpb_document_powerpoint',
	$vc_main->l('Document Word icon')       => 'wpb_document_word',
	$vc_main->l('Bookmark icon')            => 'wpb_bookmark',
	$vc_main->l('Camcorder icon')           => 'wpb_camcorder',
	$vc_main->l('Camera icon')              => 'wpb_camera',
	$vc_main->l('Chart icon')               => 'wpb_chart',
	$vc_main->l('Chart pie icon')           => 'wpb_chart_pie',
	$vc_main->l('Clock icon')               => 'wpb_clock',
	$vc_main->l('Fire icon')                => 'wpb_fire',
	$vc_main->l('Heart icon')               => 'wpb_heart',
	$vc_main->l('Mail icon')                => 'wpb_mail',
	$vc_main->l('Play icon')                => 'wpb_play',
	$vc_main->l('Shield icon')              => 'wpb_shield',
	$vc_main->l('Video icon')               => "wpb_video",
];

map([
	'name'        => $vc_main->l('Button'),
	'base'        => 'vc_button',
	'icon'        => 'icon-wpb-ui-button',
	'category'    => $vc_main->l('Content'),
	'description' => $vc_main->l('Eye catching button'),
	'params'      => [
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Text on the button'),
			'holder'      => 'button',
			'class'       => 'wpb_button',
			'param_name'  => 'title',
			'value'       => $vc_main->l('Text on the button'),
			'description' => $vc_main->l('Text on the button.'),
		],
		[
			'type'        => 'href',
			'heading'     => $vc_main->l('URL (Link)'),
			'param_name'  => 'href',
			'description' => $vc_main->l('Button link.'),
		],
		[
			'type'       => 'dropdown',
			'heading'    => $vc_main->l('Target'),
			'param_name' => 'target',
			'value'      => $target_arr,
			'dependency' => ['element' => 'href', 'not_empty' => true, 'callback' => 'vc_button_param_target_callback'],
		],
		[
			'type'               => 'dropdown',
			'heading'            => $vc_main->l('Color'),
			'param_name'         => 'color',
			'value'              => $colors_arr,
			'description'        => $vc_main->l('Button color.'),
			'param_holder_class' => 'vc_colored-dropdown',
		],
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Icon'),
			'param_name'  => 'icon',
			'value'       => $icons_arr,
			'description' => $vc_main->l('Button icon.'),
		],
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Size'),
			'param_name'  => 'size',
			'value'       => $size_arr,
			'description' => $vc_main->l('Button size.'),
		],
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Extra class name'),
			'param_name'  => 'el_class',
			'description' => $vc_main->l('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.'),
		],
	],
	'js_view'     => 'VcButtonView',
]);

map([
	'name'        => $vc_main->l('Button') . " 2",
	'base'        => 'vc_button2',
	'icon'        => 'icon-wpb-ui-button',
	'category'    => [
		$vc_main->l('Content')],
	'description' => $vc_main->l('Eye catching button'),
	'params'      => [
		[
			'type'        => 'vc_link',
			'heading'     => $vc_main->l('URL (Link)'),
			'param_name'  => 'link',
			'description' => $vc_main->l('Button link.'),
		],
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Text on the button'),
			'holder'      => 'button',
			'class'       => 'vc_btn',
			'param_name'  => 'title',
			'value'       => $vc_main->l('Text on the button'),
			'description' => $vc_main->l('Text on the button.'),
		],
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Style'),
			'param_name'  => 'style',
			'value'       => getComposerShared('button styles'),
			'description' => $vc_main->l('Button style.'),
		],
		[
			'type'               => 'dropdown',
			'heading'            => $vc_main->l('Color'),
			'param_name'         => 'color',
			'value'              => getComposerShared('colors'),
			'description'        => $vc_main->l('Button color.'),
			'param_holder_class' => 'vc_colored-dropdown',
		],
		
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Size'),
			'param_name'  => 'size',
			'value'       => getComposerShared('sizes'),
			'std'         => 'md',
			'description' => $vc_main->l('Button size.'),
		],
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Extra class name'),
			'param_name'  => 'el_class',
			'description' => $vc_main->l('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.'),
		],
	],
	'js_view'     => 'VcButton2View',
]);

/* Call to Action Button
---------------------------------------------------------- */
map([
	'name'        => $vc_main->l('Call to Action Button'),
	'base'        => 'vc_cta_button',
	'icon'        => 'icon-wpb-call-to-action',
	'category'    => $vc_main->l('Content'),
	'description' => $vc_main->l('Catch visitors attention with CTA block'),
	'params'      => [
		[
			'type'        => 'textarea',
			'admin_label' => true,
			'heading'     => $vc_main->l('Text'),
			'param_name'  => 'call_text',
			'value'       => $vc_main->l('Click edit button to change this text.'),
			'description' => $vc_main->l('Enter your content.'),
		],
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Text on the button'),
			'param_name'  => 'title',
			'value'       => $vc_main->l('Text on the button'),
			'description' => $vc_main->l('Text on the button.'),
		],
		[
			'type'        => 'href',
			'heading'     => $vc_main->l('URL (Link)'),
			'param_name'  => 'href',
			'description' => $vc_main->l('Button link.'),
		],
		[
			'type'       => 'dropdown',
			'heading'    => $vc_main->l('Target'),
			'param_name' => 'target',
			'value'      => $target_arr,
			'dependency' => ['element' => 'href', 'not_empty' => true, 'callback' => 'vc_cta_button_param_target_callback'],
		],
		[
			'type'               => 'dropdown',
			'heading'            => $vc_main->l('Color'),
			'param_name'         => 'color',
			'value'              => $colors_arr,
			'description'        => $vc_main->l('Button color.'),
			'param_holder_class' => 'vc_colored-dropdown',
		],
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Icon'),
			'param_name'  => 'icon',
			'value'       => $icons_arr,
			'description' => $vc_main->l('Button icon.'),
		],
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Size'),
			'param_name'  => 'size',
			'value'       => $size_arr,
			'description' => $vc_main->l('Button size.'),
		],
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Button position'),
			'param_name'  => 'position',
			'value'       => [
				$vc_main->l('Align right')  => 'cta_align_right',
				$vc_main->l('Align left')   => 'cta_align_left',
				$vc_main->l('Align bottom') => 'cta_align_bottom',
			],
			'description' => $vc_main->l('Select button alignment.'),
		],
		$add_css_animation,
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Extra class name'),
			'param_name'  => 'el_class',
			'description' => $vc_main->l('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.'),
		],
	],
	'js_view'     => 'VcCallToActionView',
]);

map([
	'name'        => $vc_main->l('Call to Action Button') . ' 2',
	'base'        => 'vc_cta_button2',
	'icon'        => 'icon-wpb-call-to-action',
	'category'    => [$vc_main->l('Content')],
	'description' => $vc_main->l('Catch visitors attention with CTA block'),
	'params'      => [
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Heading first line'),
			'admin_label' => true,
			//'holder' => 'h2',
			'param_name'  => 'h2',
			'value'       => $vc_main->l('Hey! I am first heading line feel free to change me'),
			'description' => $vc_main->l('Text for the first heading line.'),
		],
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Heading second line'),
			//'holder' => 'h4',
			//'admin_label' => true,
			'param_name'  => 'h4',
			'value'       => '',
			'description' => $vc_main->l('Optional text for the second heading line.'),
		],
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('CTA style'),
			'param_name'  => 'style',
			'value'       => getComposerShared('cta styles'),
			'description' => $vc_main->l('Call to action style.'),
		],
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Element width'),
			'param_name'  => 'el_width',
			'value'       => getComposerShared('cta widths'),
			'description' => $vc_main->l('Call to action element width in percents.'),
		],
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Text align'),
			'param_name'  => 'txt_align',
			'value'       => getComposerShared('text align'),
			'description' => $vc_main->l('Text align in call to action block.'),
		],
		[
			'type'        => 'colorpicker',
			'heading'     => $vc_main->l('Custom Background Color'),
			'param_name'  => 'accent_color',
			'description' => $vc_main->l('Select background color for your element.'),
		],
		[
			'type'       => 'textarea_html',
			//holder' => 'div',
			//'admin_label' => true,
			'heading'    => $vc_main->l('Promotional text'),
			'param_name' => 'content',
			'value'      => $vc_main->l('I am promo text. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.'),
		],
		[
			'type'        => 'vc_link',
			'heading'     => $vc_main->l('URL (Link)'),
			'param_name'  => 'link',
			'description' => $vc_main->l('Button link.'),
		],
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Text on the button'),
			//'holder' => 'button',
			//'class' => 'wpb_button',
			'param_name'  => 'title',
			'value'       => $vc_main->l('Text on the button'),
			'description' => $vc_main->l('Text on the button.'),
		],
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Button style'),
			'param_name'  => 'btn_style',
			'value'       => getComposerShared('button styles'),
			'description' => $vc_main->l('Button style.'),
		],
		[
			'type'               => 'dropdown',
			'heading'            => $vc_main->l('Color'),
			'param_name'         => 'color',
			'value'              => getComposerShared('colors'),
			'description'        => $vc_main->l('Button color.'),
			'param_holder_class' => 'vc_colored-dropdown',
		],
		
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Size'),
			'param_name'  => 'size',
			'value'       => getComposerShared('sizes'),
			'std'         => 'md',
			'description' => $vc_main->l('Button size.'),
		],
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Button position'),
			'param_name'  => 'position',
			'value'       => [
				$vc_main->l('Align right')  => 'right',
				$vc_main->l('Align left')   => 'left',
				$vc_main->l('Align bottom') => 'bottom',
			],
			'description' => $vc_main->l('Select button alignment.'),
		],
		$add_css_animation,
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Extra class name'),
			'param_name'  => 'el_class',
			'description' => $vc_main->l('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.'),
		],
	],
]);

/* Video element
---------------------------------------------------------- */
map([
	'name'        => $vc_main->l('Video Player'),
	'base'        => 'vc_video',
	'icon'        => 'icon-wpb-film-youtube',
	'category'    => $vc_main->l('Content'),
	'description' => $vc_main->l('Embed YouTube/Vimeo player'),
	'params'      => [
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Widget title'),
			'param_name'  => 'title',
			'description' => $vc_main->l('Enter text which will be used as widget title. Leave blank if no title is needed.'),
		],
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Video link'),
			'param_name'  => 'link',
			'admin_label' => true,
			'description' => $vc_main->l('Link to the video. '),
		],
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Extra class name'),
			'param_name'  => 'el_class',
			'description' => $vc_main->l('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.'),
		],
		[
			'type'       => 'css_editor',
			'heading'    => $vc_main->l('Css'),
			'param_name' => 'css',
			// 'description' => $vc_main->l('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.'),
			'group'      => $vc_main->l('Design options'),
		],
	],
]);

/* Google maps element
---------------------------------------------------------- */
map([
	'name'        => $vc_main->l('Google Maps'),
	'base'        => 'vc_gmaps',
	'icon'        => 'icon-wpb-map-pin',
	'category'    => $vc_main->l('Content'),
	'description' => $vc_main->l('Map block'),
	'params'      => [
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Widget title'),
			'param_name'  => 'title',
			'description' => $vc_main->l('Enter text which will be used as widget title. Leave blank if no title is needed.'),
		],
		[
			'type'        => 'textarea_safe',
			'heading'     => $vc_main->l('Map embed iframe'),
			'param_name'  => 'link',
			'description' => sprintf($vc_main->l('Visit %s to create your map. 1) Find location 2) Click "Share" and make sure map is public on the web 3) Click folder icon to reveal "Embed on my site" link 4) Copy iframe code and paste it here.'), '<a href="https://mapsengine.google.com/" target="_blank">Google maps</a>'),
		],
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Map height'),
			'param_name'  => 'size',
			'admin_label' => true,
			'description' => $vc_main->l('Enter map height in pixels. Example: 200 or leave it empty to make map responsive.'),
		],
		
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Extra class name'),
			'param_name'  => 'el_class',
			'description' => $vc_main->l('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.'),
		],
	],
]);

/* Raw HTML
---------------------------------------------------------- */
map([
	'name'          => $vc_main->l('Raw HTML'),
	'base'          => 'vc_raw_html',
	'icon'          => 'icon-wpb-raw-html',
	'category'      => $vc_main->l('Structure'),
	'wrapper_class' => 'clearfix',
	'description'   => $vc_main->l('Output raw html code on your page'),
	'params'        => [
		[
			'type'        => 'textarea_raw_html',
			'holder'      => 'div',
			'heading'     => $vc_main->l('Raw HTML'),
			'param_name'  => 'content',
			'value'       => base64_encode('<p>I am raw html block.<br/>Click edit button to change this html</p>'),
			'description' => $vc_main->l('Enter your HTML content.'),
		],
	],
]);

/* Raw JS
---------------------------------------------------------- */
map([
	'name'          => $vc_main->l('Raw JS'),
	'base'          => 'vc_raw_js',
	'icon'          => 'icon-wpb-raw-javascript',
	'category'      => $vc_main->l('Structure'),
	'wrapper_class' => 'clearfix',
	'description'   => $vc_main->l('Output raw javascript code on your page'),
	'params'        => [
		[
			'type'        => 'textarea_raw_html',
			'holder'      => 'div',
			'heading'     => $vc_main->l('Raw js'),
			'param_name'  => 'content',
			'value'       => $vc_main->l(base64_encode('<script type="text/javascript"> alert("Enter your js here!" ); </script>')),
			'description' => $vc_main->l('Enter your JS code.'),
		],
	],
]);

/* Flickr
---------------------------------------------------------- */
map([
	'base'        => 'vc_flickr',
	'name'        => $vc_main->l('Flickr Widget'),
	'icon'        => 'icon-wpb-flickr',
	'category'    => $vc_main->l('Content'),
	'description' => $vc_main->l('Image feed from your flickr account'),
	"params"      => [
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Widget title'),
			'param_name'  => 'title',
			'description' => $vc_main->l('Enter text which will be used as widget title. Leave blank if no title is needed.'),
		],
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Flickr ID'),
			'param_name'  => 'flickr_id',
			'admin_label' => true,
			'description' => sprintf($vc_main->l('To find your flickID visit %s.'), '<a href="http://idgettr.com/" target="_blank">idGettr</a>'),
		],
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Number of photos'),
			'param_name'  => 'count',
			'value'       => [9, 8, 7, 6, 5, 4, 3, 2, 1],
			'description' => $vc_main->l('Number of photos.'),
		],
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Type'),
			'param_name'  => 'type',
			'value'       => [
				$vc_main->l('User')  => 'user',
				$vc_main->l('Group') => 'group',
			],
			'description' => $vc_main->l('Photo stream type.'),
		],
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Display'),
			'param_name'  => 'display',
			'value'       => [
				$vc_main->l('Latest') => 'latest',
				$vc_main->l('Random') => 'random',
			],
			'description' => $vc_main->l('Photo order.'),
		],
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Extra class name'),
			'param_name'  => 'el_class',
			'description' => $vc_main->l('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.'),
		],
	],
]);

/* Graph
---------------------------------------------------------- */
map([
	'name'        => $vc_main->l('Progress Bar'),
	'base'        => 'vc_progress_bar',
	'icon'        => 'icon-wpb-graph',
	'category'    => $vc_main->l('Content'),
	'description' => $vc_main->l('Animated progress bar'),
	'params'      => [
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Widget title'),
			'param_name'  => 'title',
			'description' => $vc_main->l('Enter text which will be used as widget title. Leave blank if no title is needed.'),
		],
		[
			'type'        => 'exploded_textarea',
			'heading'     => $vc_main->l('Graphic values'),
			'param_name'  => 'values',
			'description' => $vc_main->l('Input graph values, titles and color here. Divide values with linebreaks (Enter). Example: 90|Development|#e75956'),
			'value'       => "90|Development,80|Design,70|Marketing",
		],
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Units'),
			'param_name'  => 'units',
			'description' => $vc_main->l('Enter measurement units (if needed) Eg. %, px, points, etc. Graph value and unit will be appended to the graph title.'),
		],
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Bar color'),
			'param_name'  => 'bgcolor',
			'value'       => [
				$vc_main->l('Grey')         => 'bar_grey',
				$vc_main->l('Blue')         => 'bar_blue',
				$vc_main->l('Turquoise')    => 'bar_turquoise',
				$vc_main->l('Green')        => 'bar_green',
				$vc_main->l('Orange')       => 'bar_orange',
				$vc_main->l('Red')          => 'bar_red',
				$vc_main->l('Black')        => 'bar_black',
				$vc_main->l('Custom Color') => 'custom',
			],
			'description' => $vc_main->l('Select bar background color.'),
			'admin_label' => true,
		],
		[
			'type'        => 'colorpicker',
			'heading'     => $vc_main->l('Bar custom color'),
			'param_name'  => 'custombgcolor',
			'description' => $vc_main->l('Select custom background color for bars.'),
			'dependency'  => ['element' => 'bgcolor', 'value' => ['custom']],
		],
		[
			'type'       => 'checkbox',
			'heading'    => $vc_main->l('Options'),
			'param_name' => 'options',
			'value'      => [
				$vc_main->l('Add Stripes?')                                      => 'striped',
				$vc_main->l('Add animation? Will be visible with striped bars.') => 'animated',
			],
		],
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Extra class name'),
			'param_name'  => 'el_class',
			'description' => $vc_main->l('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.'),
		],
	],
]);

/**
 * Pie chart
 */
map([
	'name'        => $vc_main->l('Pie chart'),
	'base'        => 'vc_pie',
	'class'       => '',
	'icon'        => 'icon-wpb-vc_pie',
	'category'    => $vc_main->l('Content'),
	'description' => $vc_main->l('Animated pie chart'),
	'params'      => [
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Widget title'),
			'param_name'  => 'title',
			'description' => $vc_main->l('Enter text which will be used as widget title. Leave blank if no title is needed.'),
			'admin_label' => true,
		],
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Pie value'),
			'param_name'  => 'value',
			'description' => $vc_main->l('Input graph value here. Choose range between 0 and 100.'),
			'value'       => '50',
			'admin_label' => true,
		],
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Pie label value'),
			'param_name'  => 'label_value',
			'description' => $vc_main->l('Input integer value for label. If empty "Pie value" will be used.'),
			'value'       => '',
		],
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Units'),
			'param_name'  => 'units',
			'description' => $vc_main->l('Enter measurement units (if needed) Eg. %, px, points, etc. Graph value and unit will be appended to the graph title.'),
		],
		[
			'type'               => 'dropdown',
			'heading'            => $vc_main->l('Bar color'),
			'param_name'         => 'color',
			'value'              => $colors_arr, //$pie_colors,
			'description'        => $vc_main->l('Select pie chart color.'),
			'admin_label'        => true,
			'param_holder_class' => 'vc_colored-dropdown',
		],
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Extra class name'),
			'param_name'  => 'el_class',
			'description' => $vc_main->l('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.'),
		],

	],
]);

/* Empty Space Element
---------------------------------------------------------- */
map([
	'name'                    => $vc_main->l('Empty Space'),
	'base'                    => 'vc_empty_space',
	'icon'                    => 'icon-wpb-ui-empty_space',
	'show_settings_on_create' => true,
	'category'                => $vc_main->l('Content'),
	'description'             => $vc_main->l('Add spacer with custom height'),
	'params'                  => [
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Height'),
			'param_name'  => 'height',
			'value'       => '32px',
			'admin_label' => true,
			'description' => $vc_main->l('Enter empty space height.'),
		],
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Extra class name'),
			'param_name'  => 'el_class',
			'description' => $vc_main->l('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.'),
		],
	],
]);

/* Custom Heading element
----------------------------------------------------------- */
map([
	'name'                    => $vc_main->l('Custom Heading'),
	'base'                    => 'vc_custom_heading',
	'icon'                    => 'icon-wpb-ui-custom_heading',
	'show_settings_on_create' => true,
	'category'                => $vc_main->l('Content'),
	'description'             => $vc_main->l('Add custom heading text with google fonts'),
	'params'                  => [
		[
			'type'        => 'textarea',
			'heading'     => $vc_main->l('Text'),
			'param_name'  => 'text',
			'admin_label' => true,
			'value'       => $vc_main->l('This is custom heading element with Google Fonts'),
			'description' => $vc_main->l('Enter your content. If you are using non-latin characters be sure to activate them under Settings/Visual Composer/General Settings.'),
		],
		[
			'type'       => 'font_container',
			'param_name' => 'font_container',
			'value'      => '',
			'settings'   => [
				'fields' => [
					'tag'                     => 'h2', // default value h2
					'text_align',
					'font_size',
					'line_height',
					'color',
					'tag_description'         => $vc_main->l('Select element tag.'),
					'text_align_description'  => $vc_main->l('Select text alignment.'),
					'font_size_description'   => $vc_main->l('Enter font size.'),
					'line_height_description' => $vc_main->l('Enter line height.'),
					'color_description'       => $vc_main->l('Select color for your element.'),
				],
			],
		],
		[
			'type'       => 'google_fonts',
			'param_name' => 'google_fonts',
			'value'      => '', 
			'settings'   => [
				'fields' => [
					'font_family'             => 'Abril Fatface:regular', 
					'font_style'              => '400 regular:400:normal', 
					'font_family_description' => $vc_main->l('Select font family.'),
					'font_style_description'  => $vc_main->l('Select font styling.'),
				],
			],
			
		],
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Extra class name'),
			'param_name'  => 'el_class',
			'description' => $vc_main->l('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.'),
		],
		[
			'type'       => 'css_editor',
			'heading'    => $vc_main->l('Css'),
			'param_name' => 'css',
			'group'      => $vc_main->l('Design options'),
		],
	],
]);

/*-----------------------New Maping Start ------------------------------*/

/*---------Start Of Vc Featured products-------------*/

map([
	'name'        => $vc_main->l('Featured Products'),
	'base'        => 'vc_featured_products',
	'class'       => '',
	'icon'        => 'vc_prestashop_icon',
	'category'    => $vc_main->l('EphenyxShop'),
	'description' => $vc_main->l('Display products set as "featured"'),
	'params'      => [

		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Title'),
			'param_name'  => 'title',
			'admin_label' => true,
		],
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Per Page'),
			'param_name'  => 'per_page',
			'value'       => '12',
			'admin_label' => true,
		],

		[
			'type'       => 'checkbox',
			'heading'    => $vc_main->l('Random'),
			'param_name' => 'random',
			'value'      => ['' => 'yes'],
		],

		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Order by'),
			'param_name'  => 'orderby',
			'value'       => [
				$vc_main->l('Product Id')     => 'id_product',
				$vc_main->l('Price')          => 'price',
				$vc_main->l('Published Date') => 'date_add',
				$vc_main->l('Product Name')   => 'name',
				$vc_main->l('Position')       => 'position',
				$vc_main->l('Manufacturer')   => 'manufacturer',
			],

			'admin_label' => true,
		],

		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Order'),
			'param_name'  => 'order',
			'value'       => [
				$vc_main->l('DESC') => 'DESC',
				$vc_main->l('ASC')  => 'ASC',
			],

			'admin_label' => true,
		],

		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Display Type'),
			'param_name'  => 'display_type',
			'value'       => [
				$vc_main->l('Grid View')    => 'grid',
				$vc_main->l('Sidebar View') => 'sidebar',
			],
			'admin_label' => true,
		],
	],
]);

/*---------End Of Vc Featured products---------------*/

/*---------Start Of Vc New products-------------*/

map([
	'name'        => $vc_main->l('New products'),
	'base'        => 'vc_new_products',
	'class'       => '',
	'icon'        => 'vc_prestashop_icon',
	'category'    => $vc_main->l('EphenyxShop'),
	'description' => $vc_main->l('Lists New products'),
	'params'      => [

		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Title'),
			'param_name'  => 'title',
			'admin_label' => true,
		],

		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Page'),
			'param_name'  => 'page',
			'value'       => '0',
			'description' => 'First page will staring from 0',
		],
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Per Page'),
			'param_name'  => 'per_page',
			'value'       => '12',
			'admin_label' => true,
		],

		[
			'type'       => 'dropdown',
			'heading'    => $vc_main->l('Order by'),
			'param_name' => 'orderby',
			'value'      => [
				$vc_main->l('Product Id')     => 'id_product',
				$vc_main->l('Price')          => 'price',
				$vc_main->l('Published Date') => 'date_add',
				$vc_main->l('Update Date')    => 'date_upd',
				$vc_main->l('Product Name')   => 'name',
			],

		],

		[
			'type'       => 'dropdown',
			'heading'    => $vc_main->l('Order'),
			'param_name' => 'order',
			'value'      => [
				$vc_main->l('DESC') => 'DESC',
				$vc_main->l('ASC')  => 'ASC',
			],
		],

		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Display Type'),
			'param_name'  => 'display_type',
			'value'       => [
				$vc_main->l('Grid View')    => 'grid',
				$vc_main->l('Sidebar View') => 'sidebar',
			],
			'admin_label' => true,
		],
	],
]);

/*------------End Of Vc New products----------------*/

/*---------Start Of Vc_bestsellers_products-------------*/

map([
	'name'        => $vc_main->l('Bestsellers Products'),
	'base'        => 'vc_bestsellers_products',
	'class'       => '',
	'icon'        => 'vc_prestashop_icon',
	'category'    => $vc_main->l('EphenyxShop'),
	'description' => $vc_main->l('List best selling products on sale'),
	'params'      => [
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Title'),
			'param_name'  => 'title',
			'admin_label' => true,
		],

		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Page'),
			'param_name'  => 'page',
			'value'       => '0',
			'description' => 'First page will staring from 0',
		],
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Per Page'),
			'param_name'  => 'per_page',
			'value'       => '12',
			'admin_label' => true,
		],

		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Order by'),
			'param_name'  => 'orderby',
			'value'       => [
				$vc_main->l('Sales')          => 'sales',
				$vc_main->l('Quantity')       => 'quantity',
				$vc_main->l('Published Date') => 'date_add',
			],
			'admin_label' => true,
		],

		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Order : '),
			'param_name'  => 'order',
			'value'       => [
				$vc_main->l('DESC') => 'DESC',
				$vc_main->l('ASC')  => 'ASC',
			],

			'admin_label' => true,
		],

		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Display Type'),
			'param_name'  => 'display_type',
			'value'       => [
				$vc_main->l('Grid View')    => 'grid',
				$vc_main->l('Sidebar View') => 'sidebar',
			],
			'admin_label' => true,
		],
	],
]);

/*---------End Of vc_bestsellers Products------------*/

/*---------Start Of Vc Special products-------------*/

map([
	'name'        => $vc_main->l('Special products'),
	'base'        => 'vc_special_products',
	'class'       => '',
	'icon'        => 'vc_prestashop_icon',
	'category'    => $vc_main->l('EphenyxShop'),
	'description' => $vc_main->l('Lists special products'),
	'params'      => [

		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Title'),
			'param_name'  => 'title',
			'admin_label' => true,
		],

		[
			'type'       => 'textfield',
			'heading'    => $vc_main->l('Page : '),
			'param_name' => 'page',
			'value'      => '0',
		],
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Per Page : '),
			'param_name'  => 'per_page',
			'value'       => '12',
			'admin_label' => true,
		],

		[
			'type'       => 'dropdown',
			'heading'    => $vc_main->l('Order by : '),
			'param_name' => 'orderby',
			'value'      => [
				$vc_main->l('Product Id')     => 'id_product',
				$vc_main->l('Price')          => 'price',
				$vc_main->l('Published Date') => 'date_add',
				$vc_main->l('Product Name')   => 'name',
			],

		],

		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Order : '),
			'param_name'  => 'order',
			'value'       => [
				$vc_main->l('DESC') => 'DESC',
				$vc_main->l('ASC')  => 'ASC',
			],

			'admin_label' => true,
		],

		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Display Type'),
			'param_name'  => 'display_type',
			'value'       => [
				$vc_main->l('Grid View')    => 'grid',
				$vc_main->l('Sidebar View') => 'sidebar',
			],
			'admin_label' => true,
		],
	],
]);


map([
	'name'        => $vc_main->l('Product Supplier'),
	'base'        => 'vc_product_supplier',
	'icon'        => 'vc_prestashop_icon',
	'category'    => $vc_main->l('EphenyxShop'),
	'description' => $vc_main->l('Show multiple products in a Supplier'),
	'params'      => [

		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Title'),
			'param_name'  => 'title',
			'admin_label' => true,
		],
		[
			'type'        => 'autocomplete',
			'heading'     => $vc_main->l('Select supplier'),
			'param_name'  => 'id_supplier',
			'description' => $vc_main->l('Enter supplier name to see suggestions'),
			'settings'    => [
				'vc_catalog_type' => 'supplier',
				'multiple'        => false,
			],
		],
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Page'),
			'value'       => 1,
			'param_name'  => 'page',
			'description' => $vc_main->l('Page to show'),
		],
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Per page'),
			'value'       => 12,
			'param_name'  => 'per_page',
			'description' => $vc_main->l('How much items per page to show'),
		],

		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Order by'),
			'param_name'  => 'orderby',
			'value'       => [
				$vc_main->l('Product id')        => 'id_product',
				$vc_main->l('Product title')     => 'pl.name',
				$vc_main->l('Price')             => 'price',
				$vc_main->l('Date Published')    => 'date_add',
				$vc_main->l('Manufacturer Name') => 'manufacturer_name',
			],
			'description' => sprintf($vc_main->l('Select how to sort retrieved products. More at %s.'), '<a href="https://dev.mysql.com/doc/refman/5.0/en/order-by-optimization.html" target="_blank">Mysql Reference</a>'),
		],
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Order way'),
			'param_name'  => 'order',
			'value'       => [
				$vc_main->l('Descending') => 'DESC',
				$vc_main->l('Ascending')  => 'ASC',
			],
			'description' => sprintf($vc_main->l('Designates the ascending or descending order. More at %s.'), '<a href="https://dev.mysql.com/doc/refman/5.0/en/order-by-optimization.html" target="_blank">Mysql Reference</a>'),
		],
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Display Type'),
			'param_name'  => 'display_type',
			'value'       => [
				$vc_main->l('Grid View')    => 'grid',
				$vc_main->l('Sidebar View') => 'sidebar',
			],
			'admin_label' => true,
		],

	],
]);



map([
	'name'        => $vc_main->l('Product Suppliers'),
	'base'        => 'vc_product_suppliers',
	'icon'        => 'vc_prestashop_icon',
	'category'    => $vc_main->l('EphenyxShop'),
	'description' => $vc_main->l('Display Suppliers Product loop'),
	'params'      => [

		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Title'),
			'param_name'  => 'title',
			'admin_label' => true,
		],
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Speed'),
			'param_name'  => 'speed',
			'description' => $vc_main->l('Display Suppliers Product loop'),
			'value'       => '500',
			'admin_label' => true,
		],
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Max Slides Number'),
			'param_name'  => 'maxslide',
			'description' => $vc_main->l('Input graph value here. Choose range between 0 and 100.'),
			'value'       => '4',
			'admin_label' => true,
		],
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Image Size'),
			'param_name'  => 'img_size',
			'value'       => $phenyxImgSizesOption,
			'description' => $vc_main->l('Set Image size for Suppliers'),
		],
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Slider Type'),
			'param_name'  => 'slider_type',
			'value'       => [
				'Bx Slider'   => 'bxslider',
				'Flex Slider' => 'flexslider',
			],
			'description' => $vc_main->l('Set Slider Type'),
		],
	],
]);

map([
	'name'        => $vc_main->l('Product'),
	'base'        => 'vc_product',
	'icon'        => 'vc_prestashop_icon',
	'category'    => $vc_main->l('EphenyxShop'),
	'description' => $vc_main->l('Show a single product'),
	'params'      => [
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Title'),
			'param_name'  => 'title',
			'admin_label' => true,
		],
		[
			'type'        => 'autocomplete',
			'heading'     => $vc_main->l('Select product'),
			'param_name'  => 'id',
			'description' => $vc_main->l('Enter product title to see suggestions'),
			'settings'    => [
				'vc_catalog_type' => 'product',
				'multiple'        => false,
			],
		],
	],
]);
/*------------------------End Of product-------------------------*/

/*------------------------Start Of products-------------------------*/

/**
 * @shortcode products
 * @description Show multiple products by ID or SKU. Make note of the plural ‘products’.
 *
 * @param columns integer
 * @param orderby array
 * @param order array
 * If the product isn’t showing, make sure it isn’t set to Hidden in the Catalog Visibility.
 */
map([
	'name'        => $vc_main->l('Products'),
	'base'        => 'vc_products',
	'icon'        => 'vc_prestashop_icon',
	'category'    => $vc_main->l('EphenyxShop'),
	'description' => $vc_main->l('Show multiple products.'),
	'params'      => [
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Title'),
			'param_name'  => 'title',
			'admin_label' => true,
		],
		[
			'type'        => 'autocomplete',
			'heading'     => $vc_main->l('Select products'),
			'param_name'  => 'ids',
			'description' => $vc_main->l('Enter product title to see suggestions'),
			'settings'    => [
				'vc_catalog_type' => 'product',
				'multiple'        => true,
			],
		],
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Order by'),
			'param_name'  => 'orderby',
			'value'       => [
				$vc_main->l('Id')           => 'id_product',
				$vc_main->l('Publish Date') => 'date_add',
				$vc_main->l('Update Date')  => 'date_upd',
				$vc_main->l('Name')         => 'name',
				$vc_main->l('Price')        => 'price',
			],
			'std'         => 'id_product',
			'description' => sprintf($vc_main->l('Select how to sort retrieved products. More at %s. Default by Title', 'js_composer'), '<a href="https://dev.mysql.com/doc/refman/5.0/en/order-by-optimization.html" target="_blank">Mysql Reference</a>'),
		],
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Order way'),
			'param_name'  => 'order',
			'value'       => [
				$vc_main->l('Descending') => 'DESC',
				$vc_main->l('Ascending')  => 'ASC',
			],
			'description' => sprintf($vc_main->l('Designates the ascending or descending order. More at %s. Default by ASC', 'js_composer'), '<a href="https://dev.mysql.com/doc/refman/5.0/en/order-by-optimization.html" target="_blank">Mysql Reference</a>'),
		],
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Display Type'),
			'param_name'  => 'display_type',
			'value'       => [
				$vc_main->l('Grid View')    => 'grid',
				$vc_main->l('Sidebar View') => 'sidebar',
			],
			'admin_label' => true,
		],

	],
]);

/*------------------------End Of Vc products-------------------------*/

/*------------------------Start Of Vc Add to Cart-------------------------*/
/**
 * @shortcode add_to_cart
 * @description Show the price and add to cart button of a single product by ID (or SKU).
 *
 * @param id integer
 * @param sku string
 * @param style string
 * If the product isn’t showing, make sure it isn’t set to Hidden in the Catalog Visibility.
 */
map([
	'name'        => $vc_main->l('Add to cart'),
	'base'        => 'vc_add_to_cart',
	'icon'        => 'vc_prestashop_icon',
	'category'    => $vc_main->l('EphenyxShop'),
	'description' => $vc_main->l('Show multiple products by ID or SKU'),
	'params'      => [
		[
			'type'        => 'autocomplete',
			'heading'     => $vc_main->l('Select product'),
			'param_name'  => 'id_product',
			'description' => $vc_main->l('Enter product title to see suggestions'),
			'settings'    => [
				'vc_catalog_type' => 'product',
				'multiple'        => false,
			],
		],
		[
			'type'       => 'textfield',
			'heading'    => $vc_main->l('Wrapper inline style', 'js_composer'),
			'param_name' => 'style',
		],
	],
]);

/*------------------------End Of Vc Add to Cart---------------------------*/

/*------------------------Start Of Vc product Category-------------------------*/

/**
 * @shortcode product_category
 * @description Show multiple products in a category by slug.
 *
 * @param per_page integer
 * @param columns integer
 * @param orderby array
 * @param order array
 * @param category string
 * Go to: WooCommerce > Products > Categories to find the slug column.
 */
map([
	'name'        => $vc_main->l('Product category'),
	'base'        => 'vc_product_category',
	'icon'        => 'vc_prestashop_icon',
	'category'    => $vc_main->l('EphenyxShop'),
	'description' => $vc_main->l('Show multiple products in a category'),
	'params'      => [

		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Title'),
			'param_name'  => 'title',
			'admin_label' => true,
		],

		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Per page'),
			'value'       => 12,
			'param_name'  => 'per_page',
			'description' => $vc_main->l('How much items per page to show'),
		],

		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Order by'),
			'param_name'  => 'orderby',
			'value'       => [
				$vc_main->l('Product id')   => 'id_product',
				$vc_main->l('Date Publish') => 'date_add',
				$vc_main->l('Date Update')  => 'date_upd',
				$vc_main->l('Price')        => 'price',
				$vc_main->l('Name')         => 'name',
				$vc_main->l('Position')     => 'position',
			],
			'description' => sprintf($vc_main->l('Select how to sort retrieved products. More at %s.'), '<a href="https://dev.mysql.com/doc/refman/5.0/en/order-by-optimization.html" target="_blank">Mysql Reference</a>'),
		],
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Order way'),
			'param_name'  => 'order',
			'value'       => [
				$vc_main->l('Descending') => 'DESC',
				$vc_main->l('Ascending')  => 'ASC',
			],
			'description' => sprintf($vc_main->l('Designates the ascending or descending order. More at %s.'), '<a href="https://dev.mysql.com/doc/refman/5.0/en/order-by-optimization.html" target="_blank">Mysql Reference</a>'),
		],
		[
			'type'        => 'autocomplete',
			'heading'     => $vc_main->l('Select category'),
			'param_name'  => 'id_category',
			'description' => $vc_main->l('Enter category name to see suggestions'),
			'settings'    => [
				'vc_catalog_type' => 'category',
				'multiple'        => false,
			],
		],
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Display Type'),
			'param_name'  => 'display_type',
			'value'       => [
				$vc_main->l('Grid View')    => 'grid',
				$vc_main->l('Sidebar View') => 'sidebar',
			],
			'admin_label' => true,
		],
	],
]);

map([
	'name'        => $vc_main->l('Product manufacturer'),
	'base'        => 'vc_product_manufacturer',
	'icon'        => 'vc_prestashop_icon',
	'category'    => $vc_main->l('EphenyxShop'),
	'description' => $vc_main->l('Show multiple products in a manufacturer'),
	'params'      => [

		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Title'),
			'param_name'  => 'title',
			'admin_label' => true,
		],
		[
			'type'        => 'autocomplete',
			'heading'     => $vc_main->l('Select Manufacturer'),
			'param_name'  => 'id_manufacturer',
			'description' => $vc_main->l('Enter manufacturer name to see suggestions'),
			'settings'    => [
				'vc_catalog_type' => 'manufacturer',
				'multiple'        => false,
			],
		],
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Per page'),
			'value'       => 12,
			'param_name'  => 'per_page',
			'description' => $vc_main->l('How much items per page to show'),
		],
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Page'),
			'value'       => 1,
			'param_name'  => 'page',
			'description' => $vc_main->l('Page to show'),
		],
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Order by'),
			'param_name'  => 'orderby',
			'value'       => [
				$vc_main->l('Product Id')        => 'id_product',
				$vc_main->l('Product Name')      => 'name',
				$vc_main->l('Manufacturer Name') => 'manufacturer_name',
				$vc_main->l('Product Quantity')  => 'quantity',
				$vc_main->l('Product Price')     => 'price',
			],
			'description' => sprintf($vc_main->l('Select how to sort retrieved products. More at %s.'), '<a href="https://dev.mysql.com/doc/refman/5.0/en/order-by-optimization.html" target="_blank">Mysql Reference</a>'),
		],
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Order way'),
			'param_name'  => 'order',
			'value'       => [
				$vc_main->l('Descending') => 'DESC',
				$vc_main->l('Ascending')  => 'ASC',
			],
			'description' => sprintf($vc_main->l('Designates the ascending or descending order. More at %s.'), '<a href="https://dev.mysql.com/doc/refman/5.0/en/order-by-optimization.html" target="_blank">Mysql Reference</a>'),
		],
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Display Type'),
			'param_name'  => 'display_type',
			'value'       => [
				$vc_main->l('Grid View')    => 'grid',
				$vc_main->l('Sidebar View') => 'sidebar',
			],
			'admin_label' => true,
		],

	],
]);

/*--------------End Of vc_manufacturer------------------*/

/*---------------------Start Of vc_manufacturers--------------------------*/

map([
	'name'        => $vc_main->l('Product Manufacturers'),
	'base'        => 'vc_product_manufacturers',
	'class'       => '',
	'icon'        => 'vc_prestashop_icon',
	'category'    => $vc_main->l('EphenyxShop'),
	'description' => $vc_main->l('Display Manufacturers Product loop'),
	'params'      => [
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Title'),
			'param_name'  => 'title',
			'admin_label' => true,
		],
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Speed'),
			'param_name'  => 'speed',
			'description' => $vc_main->l('Enter text which will be used as Speed. Leave blank if no title is needed.'),
			'value'       => '500',
			'admin_label' => true,
		],
		[
			'type'        => 'textfield',
			'heading'     => $vc_main->l('Max Slides Number'),
			'param_name'  => 'maxslide',
			'description' => $vc_main->l('Input graph value here. Choose range between 0 and 100.'),
			'value'       => '4',
			'admin_label' => true,
		],
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Image Size'),
			'param_name'  => 'img_size',
			'value'       => $phenyxImgSizesOption,
			'description' => $vc_main->l('Set Image size for Manufacturer'),
		],
		[
			'type'        => 'dropdown',
			'heading'     => $vc_main->l('Slider Type'),
			'param_name'  => 'slider_type',
			'value'       => [
				'Bx Slider'   => 'bxslider',
				'Flex Slider' => 'flexslider',
			],
			'description' => $vc_main->l('Set Slider Type'),
		],
	],
]);

