<?php

abstract class ComposerShortCodeUniversalAdmin extends ComposerShortCode {

	protected $html_editor_already_is_used = true;

	public function __construct($settings) {

		$this->settings = $settings;
		$this->addShortCode($this->settings['base'], [$this, 'output']);
	}

	protected function content($atts, $content = null) {

		return '';
	}

	public function contentAdmin($atts, $content = null) {

		$output = '';
		$this->loadParams();

		$content = $el_position = '';

		if (isset($this->settings['params'])) {
			$vc_manager = ephenyx_manager();
			$shortcode_attributes = [];

			foreach ($this->settings['params'] as $param) {

				if ($param['param_name'] != 'content') {
					$shortcode_attributes[$param['param_name']] = $param['value'];
				} else

				if ($param['param_name'] == 'content' && $content === null) {
					$content = $param['value'];
				}

			}

			$atts = Composer::shortcode_atts($shortcode_attributes, $atts);
			extract($atts);
			$editor_css_classes = apply_filters('vc_edit_form_class', ['vc_col-sm-12', 'wpb_edit_form_elements']);
			$output .= '<div class="' . implode(' ', $editor_css_classes) . '"><h2>' . $vc_manager->l('Edit') . ' ' . $this->settings['name'] . '</h2>';

			foreach ($this->settings['params'] as $param) {
				$param_value = isset($atts[$param['param_name']]) ? $atts[$param['param_name']] : null;

				if (is_array($param_value) && !empty($param['type']) && $param['type'] != 'checkbox') {

					reset($param_value);
					$first_key = key($param_value);
					$param_value = $param_value[$first_key];
				}

				$output .= $this->singleParamEditHolder($param, $param_value);
			}

			$output .= '<div class="edit_form_actions"><a href="#" class="wpb_save_edit_form button-primary">' . $vc_manager->l('Save') . '</a></div>';

			$output .= '</div>'; //close wpb_edit_form_elements
		}

		return $output;
	}

	protected function singleParamEditHolder($param, $param_value) {

		$vc_main = ephenyx_manager();
		$param['vc_single_param_edit_holder_class'] = ['wpb_el_type_' . $param['type'], 'vc_shortcode-param'];

		if (!empty($param['param_holder_class'])) {
			$param['vc_single_param_edit_holder_class'][] = $param['param_holder_class'];
		}

		$param = ComposerShortcodeEditForm::changeEditFormFieldParams($param);

		$output = '<div class="' . implode(' ', $param['vc_single_param_edit_holder_class']) . '" data-param_name="' . $vc_main->esc_attr($param['param_name']) . '" data-param_type="' . $vc_main->esc_attr($param['type']) . '" data-param_settings="' . $vc_main->esc_attr(Tools::jsonEncode($param)) . '">';
		$output .= (isset($param['heading'])) ? '<div class="wpb_element_label">' . $param['heading'] . '</div>' : '';
		$output .= '<div class="edit_form_line">';
		$output .= $this->singleParamEditForm($param, $param_value);
		$output .= (isset($param['description'])) ? '<span class="vc_description vc_clearfix">' . $param['description'] . '</span>' : '';
		$output .= '</div>';
		$output .= '</div>';
		return $output;
	}
    
    protected function generateRandomString($length = 5) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }

	protected function singleParamEditForm($param, $param_value) {

		$param_line = '';

		$vc_manager = ephenyx_manager();

		if ($param['type'] == 'textfield') {
			$value = $param_value;

			$param_line .= '<input name="' . $param['param_name'] . '" class="wpb_vc_param_value wpb-textinput ' . $param['param_name'] . ' ' . $param['type'] . '" type="text" value="' . $value . '"/>';
		}

		// Dropdown - select
		else

		if ($param['type'] == 'dropdown') {
            $selectId = $this->generateRandomString();
			$css_option = get_dropdown_option($param, $param_value);
			$param_line .= '<select id="'.$selectId.'" name="' . $param['param_name'] . '" class="wpb_vc_param_value wpb-input wpb-select ' . $param['param_name'] . ' ' . $param['type'] . ' ' . $css_option . '" data-option="' . $css_option . '">';

			if (isset($param['value'])) {

				foreach ($param['value'] as $text_val => $val) {

					if (is_numeric($text_val) && (is_string($val) || is_numeric($val))) {
						$text_val = $val;
					}

					$selected = '';

					if ($param_value !== '' && (string) $val === (string) $param_value) {
						$selected = ' selected="selected"';
					}

					$param_line .= '<option class="' . $val . '" value="' . $val . '"' . $selected . '>' . htmlspecialchars($text_val) . '</option>';
				}

			}

			$param_line .= '</select>';
            $param_line .= '<script type="text/javascript">
		      $("#'.$selectId.'").selectmenu({
				width: 645,
                classes: {
                    "ui-selectmenu-menu": "selectComposer"
                }
          });
        </script>';
		} else
		if ($param['type'] == 'animation') {
            $selectId = $this->generateRandomString();
            $param_line .= '<link rel="stylesheet" href="/content/backoffice/composer/animate.min.css" type="text/css" media="all" />';
			$param_line .= '<div class="vc_row">';
            
           
			$styles = $this->animationStyles();            
            if(is_array($param_value)) {
                
                $param_value = 'none';
            } 
            
			if (isset($this->settings['settings']['type'])) {
				$styles = $this->groupStyleByType($styles, $this->settings['settings']['type']);
			}

			if (isset($this->settings['settings']['custom']) && is_array($this->settings['settings']['custom'])) {
				$styles = array_merge($styles, $this->settings['settings']['custom']);
			}
            
			if (is_array($styles) && !empty($styles)) {
				$left_side = '<div class="vc_col-sm-6">';
                $param_line .= '<input type="hidden" class="wpb_vc_param_value animation-style" data-id="'.$selectId.'" name="' . $param['param_name'] . '" value="'.$param_value.'">';
				$build_style_select = '<select id="'.$selectId.'" name="' . $param['param_name'] . '" class="vc_param-animation-style">';

				foreach ($styles as $style) {
					$build_style_select .= '<optgroup ' . (isset($style['label']) ? 'label="' . htmlspecialchars($style['label']) . '"' : '') . '>';

					if (is_array($style['values']) && !empty($style['values'])) {

						foreach ($style['values'] as $key => $value) {
                            $selected = '';                             
                            if(isset($param_value))  {
                               
                                $val = (is_array($value) ? $value['value'] : $value);
                                if (is_string($val) && is_string($param_value) && $val === $param_value) {
						              $selected = ' selected="selected"';
					           }
                            }  
							$build_style_select .= '<option value="' . (is_array($value) ? $value['value'] : $value) . '" ' . $selected . '>' . $key . '</option>';
						}

					}

					$build_style_select .= '</optgroup>';
				}

				$build_style_select .= '</select>';
				$left_side .= $build_style_select;
				$left_side .= '</div>';
				$param_line .= $left_side;

				$right_side = '<div class="vc_col-sm-6">';
				$right_side .= '<div class="vc_param-animation-style-preview"><button class="vc_btn vc_btn-grey vc_btn-sm vc_param-animation-style-trigger">' . $vc_manager->l('Animate it') . '</button></div>';
				$right_side .= '</div>';
				$param_line .= $right_side;
			}

			$param_line .= '</div>'; // Close Row
			$param_line .= sprintf('<input name="%s" class="wpb_vc_param_value  %s %s_field" type="hidden" value="%s"  />', htmlspecialchars($param['param_name']), htmlspecialchars($param['param_name']), htmlspecialchars($param['type']), $param_value);
            $param_line .= '<script type="text/javascript">
		      $("#'.$selectId.'").selectmenu({
				width: 300,
                classes: {
                    "ui-selectmenu-menu": "selectComposer"
                },
                change: function( event, ui ) {
                    $(".wpb_vc_param_value.animation-style").val(ui.item.value);
                    var animation = ui.item.value;
                    if("none" !== animation) {
                        animation_style_test($(".vc_param-animation-style-preview"), "vc_param-animation-style-preview " + animation)
                    }
                    
                }
          });
          function animation_style_test(el, x) {
                    $(el).removeClass().addClass(x + " animated").one("webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend", function() {
                        $(this).removeClass().addClass("vc_param-animation-style-preview")
                    })
                }
        </script>';
            
		}

		// WYSIWYG field
		else

		if ($param['type'] == 'textarea_html') {

			if ($this->html_editor_already_is_used !== false) {

				$param_line .= '<div id="wp-wpb_tinymce_content-wrap" class="wp-core-ui wp-editor-wrap html-active">';
				$param_line .= '<div id="wp-wpb_tinymce_content-editor-container" class="wp-editor-container">';

				$param_line .= '<textarea id="wpb_tinymce_content" name="wpb_tinymce_content" class="wpb-textarea visual_composer_tinymce content textarea_html wp-editor-area rte autoload_rte">' . $param_value . '</textarea>';

				ob_start();
				?>
                     <script type="text/javascript">
                         $(function(){
                             var tempClass = 'visual_composer_tinymce' + Math.floor(Math.random() * 99999999).toString().padStart(8, '0');
                             $(' .visual_composer_tinymce').addClass(tempClass);
                             loadTiny(tempClass);
                          });
                    </script>
               <?php
$param_line .= ob_get_clean();

				$param_line .= '</div>';
				$param_line .= '</div>';
			} else {
				$this->html_editor_already_is_used = $param['param_name'];
				$param_line .= do_shortcode_param_settings_field('textarea_html', $param, $param_value);
			}

		}

		// Checkboxes with post types
		else

		if ($param['type'] == 'checkbox') {
			$current_value = explode(",", $param_value);
			$values = is_array($param['value']) ? $param['value'] : [];

			foreach ($values as $label => $v) {
				$checked = in_array($v, $current_value) ? ' checked="checked"' : '';
				$param_line .= ' <input id="' . $param['param_name'] . '-' . $v . '" value="' . $v . '" class="wpb_vc_param_value ' . $param['param_name'] . ' ' . $param['type'] . '" type="checkbox" name="' . $param['param_name'] . '"' . $checked . '> ' . $label;
			}

		} else

		if ($param['type'] == 'posttypes') {
			$args = [
				'public' => true,
			];
			$post_types = get_post_types($args);

			foreach ($post_types as $post_type) {
				$checked = "";

				if ($post_type != 'attachment') {

					if (in_array($post_type, explode(",", $param_value))) {
						$checked = ' checked="checked"';
					}

					$param_line .= ' <input id="' . $param['param_name'] . '-' . $post_type . '" value="' . $post_type . '" class="wpb_vc_param_value ' . $param['param_name'] . ' ' . $param['type'] . '" type="checkbox" name="' . $param['param_name'] . '"' . $checked . '> ' . $post_type;
				}

			}

		} else

		if ($param['type'] == 'taxonomies' || $param['type'] == 'taxomonies') {
			$post_types = get_post_types(['public' => false, 'name' => 'attachment'], 'names', 'NOT');

			foreach ($post_types as $type) {
				$taxonomies = get_object_taxonomies($type, '');

				foreach ($taxonomies as $tax) {
					$checked = "";

					if (in_array($tax->name, explode(",", $param_value))) {
						$checked = ' checked="checked"';
					}

					$param_line .= ' <label data-post-type="' . $type . '"><input id="' . $param['param_name'] . '-' . $tax->name . '" value="' . $tax->name . '" data-post-type="' . $type . '" class="wpb_vc_param_value ' . $param['param_name'] . ' ' . $param['type'] . '" type="checkbox" name="' . $param['param_name'] . '"' . $checked . '> ' . $tax->label . '</label>';
				}

			}

		}

		// Exploded textarea
		else

		if ($param['type'] == 'exploded_textarea') {
			$param_value = str_replace(",", "\n", $param_value);
			$param_line .= '<textarea name="' . $param['param_name'] . '" class="wpb_vc_param_value wpb-textarea ' . $param['param_name'] . ' ' . $param['type'] . '">' . $param_value . '</textarea>';
		}

		// Big Regular textarea
		else

		if ($param['type'] == 'textarea_raw_html') {
			// $param_value = $param_value;
			$param_line .= '<textarea name="' . $param['param_name'] . '" class="wpb_vc_param_value wpb-textarea_raw_html ' . $param['param_name'] . ' ' . $param['type'] . '" rows="16">' . htmlentities(rawurldecode(base64_decode($param_value)), ENT_COMPAT, 'UTF-8') . '</textarea>';
		}
        else

		if ($param['type'] == 'textarea_raw_code') {
           
            $param_line .= '<input type="hidden" id="ace_textarea_raw_code" class="wpb_vc_param_value wpb-textarea_code_html ' . $param['param_name'] . ' ' . $param['type'] . '"  name="' . $param['param_name'] . '" value="' . $param_value . '">';
			$param_line .= '<div class="ace-editor" id="ace_' . $param['param_name'] . '">' . $param_value . '</div>';
            $param_line .= '<script type="text/javascript">
		      $(document).ready(function(){
                initComposerAce("ace_' . $param['param_name'] . '", false, true);
				
	           });
            </script>';
		}


		// Big Regular textarea
		else

		if ($param['type'] == 'textarea_safe') {
			// $param_value = $param_value;
			$param_line .= '<textarea name="' . $param['param_name'] . '" class="wpb_vc_param_value wpb-textarea_raw_html ' . $param['param_name'] . ' ' . $param['type'] . '">' . value_from_safe($param_value, true) . '</textarea>';
		}

		// Regular textarea
		else

		if ($param['type'] == 'textarea') {
			$param_value = $param_value;
			$param_line .= '<textarea name="' . $param['param_name'] . '" class="wpb_vc_param_value wpb-textarea ' . $param['param_name'] . ' ' . $param['type'] . '">' . $param_value . '</textarea>';
		}

		// Attach images
		else

		if ($param['type'] == 'attach_images') {

			$param_value = removeNotExistingImgIDs($param_value);
			$param_line .= '<script type="text/javascript">';
			$param_line .= 'var imgpath = "composer/";';
			$param_line .= '</script>';
			$param_line .= '<input type="hidden" class="wpb_vc_param_value gallery_widget_attached_images_ids ' . $param['param_name'] . ' ' . $param['type'] . '" name="' . $param['param_name'] . '" value="' . $param_value . '"/>';
			$param_line .= '<div class="gallery_widget_attached_images">';
			$param_line .= '<ul class="gallery_widget_attached_images_list">';
			$param_line .= ($param_value != '') ? phenyxFieldAttachedImages(explode(",", $param_value)) : '';
			$param_line .= '</ul>';
			$param_line .= '</div>';
			$param_line .= '<div class="gallery_widget_site_images">';
			$param_line .= '</div>';
			$param_line .= '<a class="gallery_widget_add_images" href="#" title="' . $vc_manager->l('Add images') . '">' . $vc_manager->l('Add images') . '</a>';
		} else

		if ($param['type'] == 'attach_image') {
			$param_value = removeNotExistingImgIDs(preg_replace('/[^\d]/', '', $param_value));
			$param_line .= '<script type="text/javascript">';
			$param_line .= 'var imgpath = "composer/";';
			$param_line .= '</script>';
			$param_line .= '<input type="hidden" class="wpb_vc_param_value gallery_widget_attached_images_ids ' . $param['param_name'] . ' ' . $param['type'] . '" name="' . $param['param_name'] . '" value="' . $param_value . '"/>';
			$param_line .= '<div class="gallery_widget_attached_images">';
			$param_line .= '<ul class="gallery_widget_attached_images_list">';
			$param_line .= ($param_value != '') ? phenyxFieldAttachedImages(explode(",", $param_value)) : '';
			$param_line .= '</ul>';
			$param_line .= '</div>';
			$param_line .= '<div class="gallery_widget_site_images">';
			$param_line .= '</div>';
			$param_line .= '<a class="gallery_widget_add_images" href="#" use-single="true" title="' . $vc_manager->l('Add image') . '">' . $vc_manager->l('Add image') . '</a>';
		}

		//
		else

		if ($param['type'] == 'widgetised_sidebars') {
			$wpb_sidebar_ids = [];
			$sidebars = $GLOBALS['wp_registered_sidebars'];

			$param_line .= '<select name="' . $param['param_name'] . '" class="wpb_vc_param_value dropdown wpb-input wpb-select ' . $param['param_name'] . ' ' . $param['type'] . '">';

			foreach ($sidebars as $sidebar) {
				$selected = '';

				if ($sidebar["id"] == $param_value) {
					$selected = ' selected="selected"';
				}

				$sidebar_name = $sidebar["name"];
				$param_line .= '<option value="' . $sidebar["id"] . '"' . $selected . '>' . $sidebar_name . '</option>';
			}

			$param_line .= '</select>';
		} else {


			$param_line .= do_shortcode_param_settings_field($param['type'], $param, $param_value);
		}

		return $param_line;
	}

	protected function getTinyHtmlTextArea($param_value, $param = []) {

		$param_line = '';

		if (function_exists('wp_editor')) {
			$default_content = $param_value;
			$output_value = '';
			ob_start();
			wp_editor($default_content, 'wpb_tinymce_' . $param['param_name'], ['editor_class' => 'wpb_vc_param_value wpb-textarea visual_composer_tinymce ' . $param['param_name'] . ' ' . $param['type'], 'media_buttons' => true, 'wpautop' => true]);
			$output_value = ob_get_contents();
			ob_end_clean();
			$param_line .= $output_value;
		}

		return $param_line;
	}

	protected function animationStyles() {

        $vc_manager = ephenyx_manager();
		$styles = [
			[
				'values' => [
					$vc_manager->l('None') => 'none',
				],
			],
			[
				'label'  => $vc_manager->l('Attention Seekers'),
				'values' => [
					// text to display => value
					$vc_manager->l('bounce')     => [
						'value' => 'bounce',
						'type'  => 'other',
					],
					$vc_manager->l('flash')      => [
						'value' => 'flash',
						'type'  => 'other',
					],
					$vc_manager->l('pulse')      => [
						'value' => 'pulse',
						'type'  => 'other',
					],
					$vc_manager->l('rubberBand') => [
						'value' => 'rubberBand',
						'type'  => 'other',
					],
					$vc_manager->l('shake')      => [
						'value' => 'shake',
						'type'  => 'other',
					],
					$vc_manager->l('swing')      => [
						'value' => 'swing',
						'type'  => 'other',
					],
					$vc_manager->l('tada')       => [
						'value' => 'tada',
						'type'  => 'other',
					],
					$vc_manager->l('wobble')     => [
						'value' => 'wobble',
						'type'  => 'other',
					],
				],
			],
			[
				'label'  => $vc_manager->l('Bouncing Entrances'),
				'values' => [
					// text to display => value
					$vc_manager->l('bounceIn')      => [
						'value' => 'bounceIn',
						'type'  => 'in',
					],
					$vc_manager->l('bounceInDown')  => [
						'value' => 'bounceInDown',
						'type'  => 'in',
					],
					$vc_manager->l('bounceInLeft')  => [
						'value' => 'bounceInLeft',
						'type'  => 'in',
					],
					$vc_manager->l('bounceInRight') => [
						'value' => 'bounceInRight',
						'type'  => 'in',
					],
					$vc_manager->l('bounceInUp')    => [
						'value' => 'bounceInUp',
						'type'  => 'in',
					],
				],
			],
			[
				'label'  => $vc_manager->l('Bouncing Exits'),
				'values' => [
					// text to display => value
					$vc_manager->l('bounceOut')      => [
						'value' => 'bounceOut',
						'type'  => 'out',
					],
					$vc_manager->l('bounceOutDown')  => [
						'value' => 'bounceOutDown',
						'type'  => 'out',
					],
					$vc_manager->l('bounceOutLeft')  => [
						'value' => 'bounceOutLeft',
						'type'  => 'out',
					],
					$vc_manager->l('bounceOutRight') => [
						'value' => 'bounceOutRight',
						'type'  => 'out',
					],

					$vc_manager->l('bounceOutUp')    => [
						'value' => 'bounceOutUp',
						'type'  => 'out',
					],
				],
			],
			[
				'label'  => $vc_manager->l('Fading Entrances'),
				'values' => [
					// text to display => value
					$vc_manager->l('fadeIn')         => [
						'value' => 'fadeIn',
						'type'  => 'in',
					],
					$vc_manager->l('fadeInDown')     => [
						'value' => 'fadeInDown',
						'type'  => 'in',
					],
					$vc_manager->l('fadeInDownBig')  => [
						'value' => 'fadeInDownBig',
						'type'  => 'in',
					],
					$vc_manager->l('fadeInLeft')     => [
						'value' => 'fadeInLeft',
						'type'  => 'in',
					],
					$vc_manager->l('fadeInLeftBig')  => [
						'value' => 'fadeInLeftBig',
						'type'  => 'in',
					],
					$vc_manager->l('fadeInRight')    => [
						'value' => 'fadeInRight',
						'type'  => 'in',
					],
					$vc_manager->l('fadeInRightBig') => [
						'value' => 'fadeInRightBig',
						'type'  => 'in',
					],
					$vc_manager->l('fadeInUp')       => [
						'value' => 'fadeInUp',
						'type'  => 'in',
					],
					$vc_manager->l('fadeInUpBig')    => [
						'value' => 'fadeInUpBig',
						'type'  => 'in',
					],
				],
			],
			[
				'label'  => $vc_manager->l('Fading Exits'),
				'values' => [
					$vc_manager->l('fadeOut')         => [
						'value' => 'fadeOut',
						'type'  => 'out',
					],
					$vc_manager->l('fadeOutDown')     => [
						'value' => 'fadeOutDown',
						'type'  => 'out',
					],
					$vc_manager->l('fadeOutDownBig')  => [
						'value' => 'fadeOutDownBig',
						'type'  => 'out',
					],
					$vc_manager->l('fadeOutLeft')     => [
						'value' => 'fadeOutLeft',
						'type'  => 'out',
					],
					$vc_manager->l('fadeOutLeftBig')  => [
						'value' => 'fadeOutLeftBig',
						'type'  => 'out',
					],
					$vc_manager->l('fadeOutRight')    => [
						'value' => 'fadeOutRight',
						'type'  => 'out',
					],
					$vc_manager->l('fadeOutRightBig') => [
						'value' => 'fadeOutRightBig',
						'type'  => 'out',
					],
					$vc_manager->l('fadeOutUp')       => [
						'value' => 'fadeOutUp',
						'type'  => 'out',
					],
					$vc_manager->l('fadeOutUpBig')    => [
						'value' => 'fadeOutUpBig',
						'type'  => 'out',
					],
				],
			],
			[
				'label'  => $vc_manager->l('Flippers'),
				'values' => [
					$vc_manager->l('flip')     => [
						'value' => 'flip',
						'type'  => 'other',
					],
					$vc_manager->l('flipInX')  => [
						'value' => 'flipInX',
						'type'  => 'in',
					],
					$vc_manager->l('flipInY')  => [
						'value' => 'flipInY',
						'type'  => 'in',
					],
					$vc_manager->l('flipOutX') => [
						'value' => 'flipOutX',
						'type'  => 'out',
					],
					$vc_manager->l('flipOutY') => [
						'value' => 'flipOutY',
						'type'  => 'out',
					],
				],
			],
			[
				'label'  => $vc_manager->l('Lightspeed'),
				'values' => [
					$vc_manager->l('lightSpeedIn')  => [
						'value' => 'lightSpeedIn',
						'type'  => 'in',
					],
					$vc_manager->l('lightSpeedOut') => [
						'value' => 'lightSpeedOut',
						'type'  => 'out',
					],
				],
			],
			[
				'label'  => $vc_manager->l('Rotating Entrances'),
				'values' => [
					$vc_manager->l('rotateIn')          => [
						'value' => 'rotateIn',
						'type'  => 'in',
					],
					$vc_manager->l('rotateInDownLeft')  => [
						'value' => 'rotateInDownLeft',
						'type'  => 'in',
					],
					$vc_manager->l('rotateInDownRight') => [
						'value' => 'rotateInDownRight',
						'type'  => 'in',
					],
					$vc_manager->l('rotateInUpLeft')    => [
						'value' => 'rotateInUpLeft',
						'type'  => 'in',
					],
					$vc_manager->l('rotateInUpRight')   => [
						'value' => 'rotateInUpRight',
						'type'  => 'in',
					],
				],
			],
			[
				'label'  => $vc_manager->l('Rotating Exits'),
				'values' => [
					$vc_manager->l('rotateOut')          => [
						'value' => 'rotateOut',
						'type'  => 'out',

					],
					$vc_manager->l('rotateOutDownLeft')  => [
						'value' => 'rotateOutDownLeft',
						'type'  => 'out',
					],
					$vc_manager->l('rotateOutDownRight') => [
						'value' => 'rotateOutDownRight',
						'type'  => 'out',
					],
					$vc_manager->l('rotateOutUpLeft')    => [
						'value' => 'rotateOutUpLeft',
						'type'  => 'out',
					],
					$vc_manager->l('rotateOutUpRight')   => [
						'value' => 'rotateOutUpRight',
						'type'  => 'out',
					],
				],
			],
			[
				'label'  => $vc_manager->l('Specials'),
				'values' => [
					$vc_manager->l('hinge')   => [
						'value' => 'hinge',
						'type'  => 'out',
					],
					$vc_manager->l('rollIn')  => [
						'value' => 'rollIn',
						'type'  => 'in',
					],
					$vc_manager->l('rollOut') => [
						'value' => 'rollOut',
						'type'  => 'out',
					],
				],
			],
			[
				'label'  => $vc_manager->l('Zoom Entrances'),
				'values' => [
					$vc_manager->l('zoomIn')      => [
						'value' => 'zoomIn',
						'type'  => 'in',
					],
					$vc_manager->l('zoomInDown')  => [
						'value' => 'zoomInDown',
						'type'  => 'in',
					],
					$vc_manager->l('zoomInLeft')  => [
						'value' => 'zoomInLeft',
						'type'  => 'in',
					],
					$vc_manager->l('zoomInRight') => [
						'value' => 'zoomInRight',
						'type'  => 'in',
					],
					$vc_manager->l('zoomInUp')    => [
						'value' => 'zoomInUp',
						'type'  => 'in',
					],
				],
			],
			[
				'label'  => $vc_manager->l('Zoom Exits'),
				'values' => [
					$vc_manager->l('zoomOut')      => [
						'value' => 'zoomOut',
						'type'  => 'out',
					],
					$vc_manager->l('zoomOutDown')  => [
						'value' => 'zoomOutDown',
						'type'  => 'out',
					],
					$vc_manager->l('zoomOutLeft')  => [
						'value' => 'zoomOutLeft',
						'type'  => 'out',
					],
					$vc_manager->l('zoomOutRight') => [
						'value' => 'zoomOutRight',
						'type'  => 'out',
					],
					$vc_manager->l('zoomOutUp')    => [
						'value' => 'zoomOutUp',
						'type'  => 'out',
					],
				],
			],
			[
				'label'  => $vc_manager->l('Slide Entrances'),
				'values' => [
					$vc_manager->l('slideInDown')  => [
						'value' => 'slideInDown',
						'type'  => 'in',
					],
					$vc_manager->l('slideInLeft')  => [
						'value' => 'slideInLeft',
						'type'  => 'in',
					],
					$vc_manager->l('slideInRight') => [
						'value' => 'slideInRight',
						'type'  => 'in',
					],
					$vc_manager->l('slideInUp')    => [
						'value' => 'slideInUp',
						'type'  => 'in',
					],
				],
			],
			[
				'label'  => $vc_manager->l('Slide Exits'),
				'values' => [
					$vc_manager->l('slideOutDown')  => [
						'value' => 'slideOutDown',
						'type'  => 'out',
					],
					$vc_manager->l('slideOutLeft')  => [
						'value' => 'slideOutLeft',
						'type'  => 'out',
					],
					$vc_manager->l('slideOutRight') => [
						'value' => 'slideOutRight',
						'type'  => 'out',
					],
					$vc_manager->l('slideOutUp')    => [
						'value' => 'slideOutUp',
						'type'  => 'out',
					],
				],
			],
		];

		/**
		 * Used to override animation style list
		 * @since 4.4
		 */


		return $styles;
	}

	public function groupStyleByType($styles, $type) {

		$grouped = [];

		foreach ($styles as $group) {
			$inner_group = ['values' => []];

			if (isset($group['label'])) {
				$inner_group['label'] = $group['label'];
			}

			foreach ($group['values'] as $key => $value) {

				if ((is_array($value) && isset($value['type']) && ((is_string($type) && $value['type'] === $type) || is_array($type) && in_array($value['type'], $type, true))) || !is_array($value) || !isset($value['type'])) {
					$inner_group['values'][$key] = $value;
				}

			}

			if (!empty($inner_group['values'])) {
				$grouped[] = $inner_group;
			}

		}

		return $grouped;
	}

}
