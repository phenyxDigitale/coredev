<?php

class RevSliderWpml extends RevSliderFunction {

	private $cur_lang;

	/**
	 * load the wpml filters ect.
	 **/
	public function __construct() {

		RevLoader::add_action('revslider_header_content', [$this, 'add_javascript_language']);
	}

	public static function wpml_exists() {

		return true;

		if (class_exists("SitePress")) {
			return (true);
		} else {
			return (false);
		}

	}

	private static function validateWpmlExists() {

		if (!self::isWpmlExists()) {
			UniteFunctionsRev::throwError("The wpml plugin don't exists");
		}

	}

	public static function getArrLanguages($getAllCode = true) {

		$arrLangs = Language::getLanguages();

		$response = [];

		if ($getAllCode == true) {
			$response["all"] = RevLoader::__("All Languages", REVSLIDER_TEXTDOMAIN);
		}

		foreach ($arrLangs as $code => $arrLang) {
			$ind = $arrLang['iso_code'];
			$response[$ind] = $arrLang['name'];
		}

		return ($response);
	}

	public static function getArrLangCodes($getAllCode = true) {

		$arrCodes = [];

		if ($getAllCode == true) {
			$arrCodes["all"] = "all";
		}

		$arrLangs = Language::getLanguages();

		foreach ($arrLangs as $code => $arr) {
			$ind = $arr['iso_code'];

			$arrCodes[$ind] = $ind;
		}

		return ($arrCodes);
	}

	public static function isAllLangsInArray($arrCodes) {

		$arrAllCodes = self::getArrLangCodes();

		$diff = array_diff($arrAllCodes, $arrCodes);

		return (empty($diff));
	}

	public static function getFlagUrl($code) {

		$arrLangs = Language::getLanguages();

		if ($code == 'all') {
			$url = RevLoader::get_plugin_url() . '/views/img/icon16.png';
		} else {
			$url = '';

			foreach ($arrLangs as $lang) {

				if ($lang['iso_code'] == $code) {
					$url = _THEME_LANG_DIR_ . $lang['id_lang'] . '.jpg';
				}

			}

		}

		return ($url);
	}

	public static function getLangTitle($code) {

		$langs = self::getArrLanguages();

		if ($code == "all") {
			return (RevLoader::__("All Languages", REVSLIDER_TEXTDOMAIN));
		}

		if (array_key_exists($code, $langs)) {
			return ($langs[$code]);
		}

		$details = self::getLangDetails($code);

		if (!empty($details)) {
			return ($details["english_name"]);
		}

		return ("");
	}

	public static function getCurrentLang() {

		$language = Context::getContext()->language;

		$lang = $language->iso_code;

		return ($lang);
	}

	public function translate_category_lang($data, $type) {

		$cat_id = $this->get_val($data, 'cat_id');
		$cat_id = (strpos($cat_id, ',') !== false) ? explode(',', $cat_id) : [$cat_id];

		if ($this->wpml_exists()) {
			//translate categories to languages
			$newcat = [];

			foreach ($cat_id as $id) {
				$newcat[] = RevLoader::apply_filters('wpml_object_id', $id, 'category', true);
			}

			$data['cat_id'] = implode(',', $newcat);
		}

		return $data;
	}

	public function change_lang($lang, $published, $gal_ids, $slider) {

		if ($this->wpml_exists() && $slider->get_param('use_wpml', 'off') == 'on') {
			$this->cur_lang = RevLoader::apply_filters('wpml_current_language', null);
			RevLoader::do_action('wpml_switch_language', $lang);
		}

	}

	public function change_lang_to_orig($lang, $published, $gal_ids, $slider) {

		if ($this->wpml_exists() && $slider->get_param(['general', 'useWPML'], false) == true) {
			RevLoader::do_action('wpml_switch_language', $this->cur_lang);
		}

	}

	public function get_language($use_wpml, $slider) {

		$lang = self::getCurrentLang();

		return $lang;
	}

	public function get_slider_language($slider) {

		$use_wmpl = $slider->get_param(['general', 'useWPML'], false);

		return $this->get_language($use_wmpl, $slider);
	}

	public function add_javascript_language($rsad) {

		if (!$this->wpml_exists()) {
			return '';
		}

		$langs = $this->getArrLanguages();

		$use_langs = [];

		foreach ($langs as $code => $lang) {
			$use_langs[$code] = [
				'title' => $lang,
				'image' => $this->getFlagUrl($code),
			];
		}

		echo '<script type="text/javascript">';
		echo 'var RS_WPML_LANGS = JSON.parse(\'' . json_encode($use_langs) . '\');';
		echo '</script>';
	}

}
