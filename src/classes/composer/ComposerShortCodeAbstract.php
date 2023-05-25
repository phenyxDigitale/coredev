<?php

abstract class ComposerShortCodeAbstract {

	public static $config;
	protected $is_plugin = true;
	protected $is_theme = false;
	protected $disable_updater = false;
	protected $settings_as_theme = false;
	protected $as_network_plugin = false;
	protected $is_init = false;
	protected $controls_css_settings = 'cc';
	protected $controls_list = ['edit', 'clone', 'delete'];
	public function __construct() {}

	public function init($settings) {

		self::$config = (array) $settings;
	}
    
    public function l($string, $idLang = null, Context $context = null) {

        $class = 'ComposerShortCodeAbstract';

        return Translate::getClassTranslation($string, $class);
    }

	public function addAction($action, $method, $priority = 10) {

		if (method_exists($this, $method)) {

			if (!isset(Composer::$VCBackofficeShortcodesAction[$action])) {
				Composer::$VCBackofficeShortcodesAction[$action] = [];
			}

			Composer::$VCBackofficeShortcodesAction[$action][] = [&$this, $method];
		}

	}

	public function addFilter($filter, $method, $priority = 10) {

		return true;
	}

    public function addShortCode( $tag, $func ) {                        
			Composer::add_shortcode( $tag, $func );//change this like wp shortcodes..
	}

	public function doShortCode( $content ) {
		Composer::do_shortcode( $content );
	}

	public function removeShortCode( $tag ) {
		Composer::remove_shortcode( $tag );
	}

	/* Shortcode methods */
	

	public function post($param) {

		return isset($_POST[$param]) ? $_POST[$param] : null;
	}

	public function get($param) {

		return isset($_GET[$param]) ? $_GET[$param] : null;
	}

	
	public function assetPath($asset) {

		return self::$config['APP_ROOT'] . self::$config['ASSETS_DIR'] . $asset;
	}

	public static function config($name) {

		return isset(self::$config[$name]) ? self::$config[$name] : null;
	}

}
