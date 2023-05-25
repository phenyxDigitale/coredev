<?php

class ComposerShortCodeParams {

	/**
	 * @var array - store shortcode attributes types
	 */
	protected static $params = [];
	/**
	 * @var array - store shortcode javascript files urls
	 */
	protected static $scripts = [];
	protected static $enqueue_script = [];
	protected static $scripts_to_register = [];
	protected static $is_enqueue = false;

	public static function registerScript($script) {

		$script_name = 'vc_edit_form_enqueue_script_' . md5($script);
		self::$enqueue_script[] = ['name' => $script_name, 'script' => $script];
	}

	public static function addField($name, $form_field_callback, $script_url = null) {

		$result = false;

		if (!empty($name) && !empty($form_field_callback)) {
			self::$params[$name] = [
				'callbacks' => [
					'form' => $form_field_callback,
				],
			];
			$result = true;

			if (is_string($script_url) && !in_array($script_url, self::$scripts)) {
				self::registerScript($script_url);
				self::$scripts[] = $script_url;
			}

		}

		return $result;
	}

	public static function renderSettingsField($name, $param_settings, $param_value) {

		if (isset(self::$params[$name]['callbacks']['form'])) {
			return call_user_func(self::$params[$name]['callbacks']['form'], $param_settings, $param_value);
		}

		return '';
	}

	public static function getScripts() {

		return self::$scripts;
	}

	public static function setEnqueue($value) {

		self::$is_enqueue = (boolean) $value;
	}

	public static function isEnqueue() {

		return self::$is_enqueue;
	}

}
