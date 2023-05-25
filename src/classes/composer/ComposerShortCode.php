<?php

define('SHORTCODE_CUSTOMIZE_PREFIX', 'vc_theme_');
define('SHORTCODE_BEFORE_CUSTOMIZE_PREFIX', 'vc_theme_before_');
define('SHORTCODE_AFTER_CUSTOMIZE_PREFIX', 'vc_theme_after_');
define('SHORTCODE_CUSTOM_CSS_FILTER_TAG', 'vc_shortcodes_css_class');

abstract class ComposerShortCode extends ComposerShortCodeAbstract {

	public $context;
    protected $shortcode;
	protected $html_template;
	protected $atts, $settings;
	protected static $enqueue_index = 0;
	protected static $js_scripts = [];
	protected static $css_scripts = [];
	protected $shortcode_string = '';
	protected $controls_template_file = 'editors/partials/backend_controls.tpl';
	
    public function __construct($settings, $renderFront = false) {

        
		$this->settings = $settings;
		$this->shortcode = $this->settings('base');

	}

	public function getShortcode() {

		return $this->shortcode;
	}

	public function addInlineAnchors($content) {

		return ($this->isInline() || $this->isEditor() && $this->settings('is_container') === true ? '<span class="vc_container-anchor"></span>' : '') . $content;
	}
    
    public function addShortCode($tag, $func) {

		Composer::add_shortcode($tag, $func); //change this like wp shortcodes..
	}

	public function doShortCode($content) {

		Composer::do_shortcode($content);
	}

	public function removeShortCode($tag) {

		Composer::remove_shortcode($tag);
	}

	

	protected function registerJs($param) {

		Composer::$registeredJS[] = $param;

	}

	protected function registerCss($param) {

		Composer::$registeredCSS[] = $param; // to load css in prestashop displayBackOfficeHeader hook

	}


	public function shortcode($shortcode) {}

	protected function setTemplate($template) {

		$this->html_template = $template;
	}

	protected function getTemplate() {

		if (isset($this->html_template)) {
			return $this->html_template;
		}

		return false;
	}

	protected function getFileName() {

        
		return $this->shortcode;
	}

	/**
	 * Find html template for shortcode output.
	 */
	protected function findShortcodeTemplate() {
		
       
        if ( ! empty( $this->settings['html_template'] ) && is_file( $this->settings( 'html_template' ) ) ) {
            
		  return $this->setTemplate( $this->settings['html_template'] );
		}
        $file_name = $this->getFilename().'.php';
		// Check template in theme directory
		$user_template = DIGITAL_CORE_DIR. '/src/classes/shortcodes/'.$file_name;
       
		if (is_file($user_template)) {
              
            $result = $this->setTemplate($user_template);
           
			return $this->html_template;
		} else {
            $override_template = Hook::exec('actionOverrideComposerTemplate', ['file_name' => $file_name]);
            if(is_file($override_template)) {
                
                $result = $this->setTemplate($override_template);           
                return $this->html_template;
            }
            
            $this->html_template = false;
        }

	}

	protected function content($atts, $content = null) {

		return $this->loadTemplate($atts, $content);
	}

	protected function loadTemplate($atts, $content = null) {       
           
       
		$output = '';
		$html_template = $this->findShortcodeTemplate();
       
		if (is_file($html_template)) {
             
			ob_start();
             include ($html_template) ;			
			$output = ob_get_contents();
			ob_end_clean();
		} else {
            
            throw new PhenyxException('Template file is missing for `' . $this->shortcode . '` shortcode. Make sure you have `' . $this->html_template . '` file in your theme folder.');
		}
        
		return $output;
	}

	public function contentAdmin($atts, $content = null) {
        
        $file = fopen("testcontentAdmin.txt","a");
        fwrite($file,$this->shortcode.PHP_EOL);
		$element = $this->shortcode;
        fwrite($file,$content.PHP_EOL);
		$output = $custom_markup = $width = $el_position = '';

		if ($content != NULL) {
			$content = ephenyx_manager()->wpautop(stripslashes($content));
		}
        fwrite($file,$content.PHP_EOL);
		if (isset($this->settings['params'])) {
			$shortcode_attributes = ['width' => '1/1'];

			foreach ($this->settings['params'] as $param) {

				if ($param['param_name'] != 'content') {

					if (isset($param['value'])) {
						$shortcode_attributes[$param['param_name']] = is_string($param['value']) ? $param['value'] : $param['value'];
					} else {
						$shortcode_attributes[$param['param_name']] = '';
					}

				} else
				if ($param['param_name'] == 'content' && $content == NULL) {
					$content = isset($param['value']) ? $param['value'] : '';
				}

			}

			extract(Composer::shortcode_atts(
				$shortcode_attributes
				, $atts));
             
			$elem = $this->getElementHolder($width);

			if (isset($atts['el_position'])) {
				$el_position = $atts['el_position'];
			}

			$iner = $this->outputTitle($this->settings['name']);

			foreach ($this->settings['params'] as $param) {
				$param_value = isset($param['param_name']) ? $param['param_name'] : '';

				if (is_array($param_value)) {
					reset($param_value);
					$first_key = key($param_value);
					$param_value = is_null($first_key) ? '' : $param_value[$first_key];
				}

				$iner .= $this->singleParamHtmlHolder($param, $param_value);
			}

			$elem = str_ireplace('%wpb_element_content%', $iner, $elem);
			$output .= $elem;
		} else {
			//This is used for shortcodes without params (like simple divider)
			// $column_controls = $this->getColumnControls($this->settings['controls']);
			$width = '1/1';

			$elem = $this->getElementHolder($width);

			$inner = '';

			if (isset($this->settings["custom_markup"]) && $this->settings["custom_markup"] != '') {

				if ($content != '') {
					$custom_markup = str_ireplace("%content%", $content, $this->settings["custom_markup"]);
				} else
				if ($content == '' && isset($this->settings["default_content_in_template"]) && $this->settings["default_content_in_template"] != '') {
					$custom_markup = str_ireplace("%content%", $this->settings["default_content_in_template"], $this->settings["custom_markup"]);
				}

				//$output .= do_shortcode($this->settings["custom_markup"]);
				$inner .= do_shortcode($custom_markup);
			}

			$elem = str_ireplace('%wpb_element_content%', $inner, $elem);
			$output .= $elem;
		}

		return $output;
	}

	public function isAdmin() {
        
        if(isset($this->context->user->id)) {
            return false;
        }

		$return = isset($this->context->employee->id);
		return $return;
	}

	public function isInline() {

		return is_inline();
	}

	public function isEditor() {

		return is_editor();
	}

	public function output($atts, $content = null, $base = '') {
       
		$this->atts = $this->prepareAtts($atts);
		$output = '';
		$content = empty($content) && !empty($atts['content']) ? $atts['content'] : $content;

		if (($this->isInline() || is_page_editable()) && method_exists($this, 'contentInline')) {
           
			$output .= $this->contentInline($this->atts, $content);
		} else if ($this->isAdmin()) {
            
			$output .= $this->contentAdmin($this->atts, $content);

		}

		if (empty($output)) {
           
			$custom_output = SHORTCODE_CUSTOMIZE_PREFIX . $this->shortcode;
			$custom_output_before = SHORTCODE_BEFORE_CUSTOMIZE_PREFIX . $this->shortcode; // before shortcode function hook
			$custom_output_after = SHORTCODE_AFTER_CUSTOMIZE_PREFIX . $this->shortcode; // after shortcode function hook
			// Before shortcode

			if (function_exists($custom_output_before)) {
				$output .= $custom_output_before($this->atts, $content);
			} else {
				$output .= $this->beforeShortcode($this->atts, $content);
			}

			// Shortcode content

			if (function_exists($custom_output)) {
				$output .= $custom_output($this->atts, $content);
			} else {
				$output .= $this->content($this->atts, $content);
			}

			// After shortcode

			if (function_exists($custom_output_after)) {
				$output .= $custom_output_after($this->atts, $content);
			} else {
				$output .= $this->afterShortcode($this->atts, $content);
			}

		}

		return $output;
	}

	/**
	 * Creates html before shortcode html.
	 *
	 * @param $atts    - shortcode attributes list
	 * @param $content - shortcode content
	 * @return string - html which will be displayed before shortcode html.
	 */
	public function beforeShortcode($atts, $content) {

		return '';
	}

	/**
	 * Creates html before shortcode html.
	 *
	 * @param $atts    - shortcode attributes list
	 * @param $content - shortcode content
	 * @return string - html which will be displayed after shortcode html.
	 */
	public function afterShortcode($atts, $content) {

		return '';
	}

	public function getExtraClass($el_class) {

		$output = '';

		if ($el_class != '') {
			$output = " " . str_replace(".", "", $el_class);
		}

		return $output;
	}

	public function getCSSAnimation($css_animation) {

		$output = '';

		if (!is_array($css_animation) && $css_animation != '') {
			Context::getContext()->controller->addJS(_EPH_JS_DIR_ . 'composer/jquery-waypoints/waypoints.min.js');
			$output = ' wpb_animate_when_almost_visible wpb_' . $css_animation.' '. $css_animation;
		}

		return $output;
	}

	/**
	 * Create HTML comment for blocks
	 *
	 * @param $string
	 *
	 * @return string
	 */
	public function endBlockComment($string) {

		//return '';
		return (!empty($_GET['wpb_debug']) && $_GET['wpb_debug'] == 'true' ? '<!-- END ' . $string . ' -->' : '');
	}

	/**
	 * Start row comment for html shortcode block
	 *
	 * @param $position - block position
	 * @return string
	 */
	public function startRow($position) {

		$output = '';
		return '';
	}

	/**
	 * End row comment for html shortcode block
	 *
	 * @param $position -block position
	 * @return string
	 */

	public function endRow($position) {

		$output = '';
		return '';
	}

	public function settings($name) {

		return isset($this->settings[$name]) ? $this->settings[$name] : null;
	}

	public function setSettings($name, $value) {

		$this->settings[$name] = $value;
	}

	public function getElementHolder($width) {
               
		$output = '';
		$column_controls = $this->getColumnControlsModular();
		$css_class = 'wpb_' . $this->settings["base"] . ' wpb_content_element wpb_sortable' . (!empty($this->settings["class"]) ? ' ' . $this->settings["class"] : '');
		$output .= '<div data-element_type="' . $this->settings["base"] . '" class="' . $css_class . '">';
        //$output .= $column_controls;
		$output .= str_replace("%column_size%", translateColumnWidthToFractional($width), $column_controls);
		$output .= $this->getCallbacks($this->shortcode);
		$output .= '<div class="wpb_element_wrapper ' . $this->settings("wrapper_class") . '">';
		$output .= '%wpb_element_content%';
		$output .= '</div>'; // <!-- end .wpb_element_wrapper -->';
		$output .= '</div>'; // <!-- end #element-'.$this->shortcode.' -->';
		return $output;
	}

	/* This returs block controls
---------------------------------------------------------- */
	public function getColumnControls($controls, $extended_css = '') {

		$controls_start = '<div class="vc_controls controls controls_element' . (!empty($extended_css) ? " {$extended_css}" : '') . '">';
		$controls_end = '</div>';
		$vc_manager = ephenyx_manager();
		$controls_add = ''; //' <a class="column_add" href="#" title="'.sprintf($vc_manager->l('Add to %s'), strtolower($this->settings('name'))).'"></a>';
		$controls_edit = ' <a class="vc_control column_edit" href="#" title="' . sprintf($vc_manager->l('Edit %s'), strtolower($this->settings('name'))) . '"><span class="vc_icon"></span></a>';
		$controls_delete = ' <a class="vc_control column_clone" href="#" title="' . sprintf($vc_manager->l('Clone %s'), strtolower($this->settings('name'))) . '"><span class="vc_icon"></span></a> <a class="column_delete" href="#" title="' . sprintf($vc_manager->l('Delete %s'), strtolower($this->settings('name'))) . '"><span class="vc_icon"></span></a>';

		$column_controls_full = $controls_start . $controls_add . $controls_edit . $controls_delete . $controls_end;
		$column_controls_size_delete = $controls_start . $controls_delete . $controls_end;
		$column_controls_popup_delete = $controls_start . $controls_delete . $controls_end;
		$column_controls_edit_popup_delete = $controls_start . $controls_edit . $controls_delete . $controls_end;

		if ($controls == 'popup_delete') {
			return $column_controls_popup_delete;
		} else
		if ($controls == 'edit_popup_delete') {
			return $column_controls_edit_popup_delete;
		} else
		if ($controls == 'size_delete') {
			return $column_controls_size_delete;
		} else
		if ($controls == 'popup_delete') {
			return $column_controls_popup_delete;
		} else
		if ($controls == 'add') {
			return $controls_start . $controls_add . $controls_end;
		} else {
			return $column_controls_full;
		}

	}

	
	public function getColumnControlsModular($extended_css = '') {

        global $smarty;
        $context = Context::getContext();
        //ob_start();
       
		$data = $context->smarty->createTemplate(_EPH_COMPOSER_DIR_  .  $this->controls_template_file);
        $data->assign(
		  [
			'position'     => $this->controls_css_settings,
			'extended_css' => $extended_css,
			'name'         => $this->settings('name'),
			'controls'     => $this->controls_list,
          ]
		);
        return $data->fetch();
		//ob_get_clean();
	}

	/* This will fire callbacks if they are defined in map.php
---------------------------------------------------------- */
	public function getCallbacks($id) {

		$output = '';

		if (isset($this->settings['js_callback'])) {

			foreach ($this->settings['js_callback'] as $text_val => $val) {
				/* TODO: name explain */
				$output .= '<input type="hidden" class="wpb_vc_callback wpb_vc_' . $text_val . '_callback " name="' . $text_val . '" value="' . $val . '" />';
			}

		}

		return $output;
	}

	public function singleParamHtmlHolder($param, $value) {

		$output = '';
		$old_names = ['yellow_message', 'blue_message', 'green_message', 'button_green', 'button_grey', 'button_yellow', 'button_blue', 'button_red', 'button_orange'];
		$new_names = ['alert-block', 'alert-info', 'alert-success', 'btn-success', 'btn', 'btn-info', 'btn-primary', 'btn-danger', 'btn-warning'];
		$value = str_ireplace($old_names, $new_names, $value);
		
		$param_name = isset($param['param_name']) ? $param['param_name'] : '';
		$type = isset($param['type']) ? $param['type'] : '';
		$class = isset($param['class']) ? $param['class'] : '';

		if (!empty($param['holder'])) {

			if ($param['holder'] !== 'hidden') {
				$output .= '<' . $param['holder'] . ' class="wpb_vc_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '">' . $value . '</' . $param['holder'] . '>';
			} else if ($param['holder'] == 'input') {
				$output .= '<' . $param['holder'] . ' readonly="true" class="wpb_vc_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '" value="' . $value . '">';
			} else if (in_array($param['holder'], ['img', 'iframe'])) {
				$output .= '<' . $param['holder'] . ' class="wpb_vc_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '" src="' . $value . '">';
			}

		}

		if (!empty($param['admin_label']) && $param['admin_label'] === true) {
			$output .= '<span class="vc_admin_label admin_label_' . $param['param_name'] . (empty($value) ? ' hidden-label' : '') . '"><label>' . $param['heading'] . '</label>: ' . $value . '</span>';
		}

		return $output;
	}

	protected function outputTitle($title) {

		$icon = $this->settings('icon');

		if (filter_var($icon, FILTER_VALIDATE_URL)) {
			$icon = '';
		}

		return '<h4 class="wpb_element_title"><span class="vc_element-icon' . (!empty($icon) ? ' ' . $icon : '') . '"></span> ' . ephenyx_manager()->esc_attr($title) . '</h4>';
	}

	public function template($content = '') {

		return $this->contentAdmin($this->atts, $content);
	}

	protected function prepareAtts($atts) {

		$return = [];

		if (is_array($atts)) {

			foreach ($atts as $key => $val) {
				$return[$key] = preg_replace('/\`\`/', '"', $val);
			}

		}

		return $return;
	}

}
