<?php

class ComposerSeetings {

	protected $option_group = 'wpb_js_composer_settings';
	protected $page = "vc_settings";
	protected static $field_prefix = 'wpb_js_';
	protected static $notification_name = 'wpb_js_notify_user_about_element_class_names';
	protected static $color_settings, $defaults;
	protected $composer;
	protected $google_fonts_subsets_default = ['latin'];
	protected $google_fonts_subsets = ['latin', 'vietnamese', 'cyrillic', 'latin-ext', 'greek', 'cyrillic-ext', 'greek-ext'];
	
	public static function get($option_name) {

		return Configuration::get(self::$field_prefix . $option_name);
	}

	public static function set($option_name, $value) {

		return Configuration::updateValue(self::$field_prefix . $option_name, $value);
	}



}


?>