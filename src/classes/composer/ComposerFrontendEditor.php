<?php

class ComposerFrontendEditor {

	protected $dir;
	protected $tag_index = 1;
	public $post_shortcodes = [];
	public static $admin_body_classes;
	protected $template_content = '';
	protected static $enabled_inline = true;
	protected $settings = [
		'assets_dir'         => 'assets',
		'templates_dir'      => 'templates',
		'template_extension' => 'tpl.php',
		'plugin_path'        => 'js_composer/inline',
	];
	protected static $content_editor_id = 'content';
	protected static $content_editor_settings = [
		'dfw'               => true,
		'tabfocus_elements' => 'insert-media-button',
		'editor_height'     => 360,
	];
	protected static $brand_url = 'http://vc.wpbakery.com/?utm_campaign=VCplugin_header&utm_source=vc_user&utm_medium=frontend_editor';
	public function init() {

		$this->addHooks();
		/**
		 * If current mode of VC is frontend editor load it.
		 */

		if (vc_is_frontend_editor()) {
			ephenyx_frontend_editor()->hookLoadEdit();
		} else if (vc_mode() === 'page_editable') {
			/**
			 * if page loaded inside frontend editor iframe it has page_editable mode.
			 * It required to some some js/css elements and add few helpers for editor to be used.
			 */
			$this->buildEditablePage();
		} else {
			// Is it is simple page just enable buttons and controls
			$this->buildPage();
		}

		// Load required vendors classes;
		// visual_composer()->vendorsManager()->load();
	}

	public function addHooks() {

		JsComposer::$sds_action_hooks['vc_load_shortcode'] = [&$this, 'loadShortcodes'];
		JsComposer::add_shortcode('vc_container_anchor', 'vc_container_anchor');
	}

	public function hookLoadEdit() {

//		add_action( 'current_screen', array( &$this, 'adminInit' ) );
		JsComposer::$front_editor_actions['current_screen'] = [&$this, 'adminInit'];
	}

	public function adminInit() {

		$this->setPost();
		$this->renderEditor();

	}

	public function getIt($val_identifier) {

		$content = [];

		if (Tools::getValue('frontend_plugin_name')) {
			$plugins_configuration = JsComposer::getPluginsConfiguration();

			$frontend_plugin_name = Tools::getValue('frontend_plugin_name');

			$plugin_type = '';
			$plugin_controller = '';
			$plugin_table = '';
			$plugin_identifier = '';
			$plugin_field = '';
			$plugin_status = '';
			$plugin_frontend_status = '';
			$plugin_backend_status = '';

			foreach ($plugins_configuration as $key => $value) {

				if ($value->controller == $frontend_plugin_name) {

					$plugin_type = (isset($value->type)) ? $value->type : '';
					$plugin_controller = $value->controller;
					$field_identifier = $value->identifier;
					$field_content = $value->field;
					$db_table = (isset($value->dbtable)) ? $value->dbtable : '';
					$plugin_status = $value->plugin_status;
					$plugin_frontend_status = $value->plugin_frontend_status;
					$plugin_backend_status = $value->plugin_backend_status;

					$back_url = [];

					foreach ($_GET as $key => $value) {
						$back_url[$key] = $value;
					}

					$back_url = urlencode(serialize($back_url));

					$context = Context::getContext();
					$id_lang = $context->language->id;

					$post = '';

					if ($db_table != '') {
						$post = JsComposer::getJsControllerValues($db_table, $field_content, $field_identifier, Tools::getValue('val_identifier'), $id_lang);
					}

					// $post = new $plugin_controller((int) $val_identifier);

					$skip_hooks = ['displayNav', 'displayTop', 'displayTopColumn'];

					$content = (in_array($post->hook_name, $skip_hooks)) ? '' : $post->$field_content;
				}

			}

		}

		$vc_inline_tag = ''; //'<span id="vc_inline-anchor" style="display:none !important;"></span>';
		$controller_name = Tools::getValue('frontend_plugin_name');

		$plugin_frontend_status = JsComposer::getPluginEditorConfiguration($controller_name, 'plugin_frontend_status');

		if ($plugin_frontend_status) {
			$vc_inline_tag = '<span id="vc_inline-anchor" style="display:none !important;"></span>';
		}

		switch ($controller_name) {
		case 'AdminVcContentAnyWhere':
			$Smartlisence = new Smartlisence();
			$plugin_active_status = $Smartlisence->isActive();

			if ($plugin_active_status AND $plugin_frontend_status) {
				$vc_inline_tag = '<span id="vc_inline-anchor" style="display:none !important;"></span>';
			} else {
				$vc_inline_tag = '';
			}

			break;
		case 'AdminCategories':

			break;
		case 'AdminManufacturers':

			break;
		case 'AdminCmsContent':

			break;

		default:
			$Smartlisence = new Smartlisence();
			$plugin_active_status = $Smartlisence->isActive();

			if ($plugin_active_status AND $plugin_frontend_status) {
				$vc_inline_tag = '<span id="vc_inline-anchor" style="display:none !important;"></span>';
			} else {
				$vc_inline_tag = '';
			}

			break;
		}

		return (isset($content[$id_lang])) ? $vc_inline_tag . $content[$id_lang] : '';
	}

	public function contentAnyWhereInit($val_identifier) {

		echo $this->getIt($val_identifier);
	}

	public function buildEditablePage() {

		!defined('CONCATENATE_SCRIPTS') && define('CONCATENATE_SCRIPTS', false);
		JsComposer::$front_editor_actions['vc_content'] = [&$this, 'addContentAnchor'];
	}

	public function buildPage() {

		JsComposer::$front_editor_actions['admin_bar_menu'] = [&$this, "adminBarEditLink"];

	}

	public static function inlineEnabled() {

		if (Tools::getValue('action') == 'vc_load_shortcode') {
			self::$enabled_inline = true;
		} else if (!in_array(Tools::getValue('controller'), ['AdminCmsContent', 'VC_frontend', 'cms'])) {
			self::disableInline();
		}

		return self::$enabled_inline;
	}

	public static function disableInline($disable = true) {

		self::$enabled_inline = !$disable;
	}

	public function addContentAnchor($content = '') {

		return '<span id="vc_inline-anchor" style="display:none !important;"></span>' . $content;
	}

	public static function getInlineUrl($url = '', $id = '') {

		$plugins_configuration = JsComposer::getPluginsConfiguration();
		$url = '';
		$frontend_plugin_name = Tools::getValue('controller');

		$plugin_type = '';
		$plugin_controller = '';
		$plugin_table = '';
		$plugin_identifier = '';
		$plugin_field = '';
		$plugin_status = '';
		$plugin_frontend_status = '';
		$plugin_backend_status = '';

		foreach ($plugins_configuration as $key => $value) {

			if ($value->controller == $frontend_plugin_name) {
				$plugin_type = (isset($value->type)) ? $value->type : '';
				$plugin_controller = $value->controller;
				$plugin_identifier = $value->identifier;
				$plugin_field = $value->field;
				$plugin_status = $value->plugin_status;
				$plugin_frontend_status = $value->plugin_frontend_status;
				$plugin_backend_status = $value->plugin_backend_status;

				$back_url = [];

				foreach ($_GET as $key => $value) {
					$back_url[$key] = $value;
				}

				$back_url = urlencode(serialize($back_url));

				$val_identifier = Tools::getValue($plugin_identifier);

				$context = Context::getContext();
				$id_lang = $context->language->id;

				$url = Context::getContext()->link->getAdminLink('VC_frontend');
				$url .= "&val_identifier={$val_identifier}&frontend_plugin_name={$frontend_plugin_name}&id_lang={$id_lang}&vc_action=vc_inline&return_url=" . $back_url;
			}

		}

		return $url;

		$url = '';
		$frontend_plugin_name = Tools::getValue('controller');
		$tmp_frontend_plugin_name = strtolower($frontend_plugin_name);
		$tmp_frontend_plugin_name = str_replace("admin", "", $tmp_frontend_plugin_name);

		if (property_exists($plugins_configuration, $tmp_frontend_plugin_name)) {
			$modult_config = $plugins_configuration->$tmp_frontend_plugin_name;

			$frontend_plugin_name = str_replace("Admin", "", $frontend_plugin_name);

			// $action_identifier =
			$back_url = [];

			foreach ($_GET as $key => $value) {
				$back_url[$key] = $value;
			}

			$back_url = urlencode(serialize($back_url));

			$plugin_controller = $modult_config->controller;
			$db_table = $modult_config->table;
			$field_identifier = $modult_config->identifier;
			$field_content = $modult_config->field;

			$val_identifier = Tools::getValue($field_identifier);

			$url = Context::getContext()->link->getAdminLink('VC_frontend');
			$url .= "&val_identifier={$val_identifier}&frontend_plugin_name={$frontend_plugin_name}&id_lang={$id_lang}&vc_action=vc_inline&return_url=" . $back_url;
		}

		return $url;

	}

	function wrapperStart() {

		return '';
	}

	function wrapperEnd() {

		return '';
	}

	public static function setBrandUrl($url) {

		self::$brand_url = $url;
	}

	public static function getBrandUrl() {

		return self::$brand_url;
	}

	public static function shortcodesRegexp() {

		$tagnames = array_keys(ComposerMap::getShortCodes());

		$tagregexp = join('|', array_map('preg_quote', $tagnames));
		// WARNING from shortcodes.php! Do not change this regex without changing do_shortcode_tag() and strip_shortcode_tag()
		// Also, see shortcode_unautop() and shortcode.js.
		return
		'\\[' // Opening bracket
		 . '(\\[?)' // 1: Optional second opening bracket for escaping shortcodes: [[tag]]
		 . "($tagregexp)"// 2: Shortcode name
		 . '(?![\\w-])' // Not followed by word character or hyphen
		 . '(' // 3: Unroll the loop: Inside the opening shortcode tag
		 . '[^\\]\\/]*' // Not a closing bracket or forward slash
		 . '(?:'
		. '\\/(?!\\])' // A forward slash not followed by a closing bracket
		 . '[^\\]\\/]*' // Not a closing bracket or forward slash
		 . ')*?'
		. ')'
		. '(?:'
		. '(\\/)' // 4: Self closing tag ...
		 . '\\]' // ... and closing bracket
		 . '|'
		. '\\]' // Closing bracket
		 . '(?:'
		. '(' // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags
		 . '[^\\[]*+' // Not an opening bracket
		 . '(?:'
		. '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag
		 . '[^\\[]*+' // Not an opening bracket
		 . ')*+'
		. ')'
		. '\\[\\/\\2\\]' // Closing shortcode tag
		 . ')?'
			. ')'
			. '(\\]?)'; // 6: Optional second closing brocket for escaping shortcodes: [[tag]]

	}

	function setPost() {

		if (Tools::getValue('frontend_plugin_name')) {
			$plugins_configuration = JsComposer::getPluginsConfiguration();

			$frontend_plugin_name = Tools::getValue('frontend_plugin_name');

			$plugin_type = '';
			$plugin_controller = '';
			$plugin_table = '';
			$plugin_identifier = '';
			$plugin_field = '';
			$plugin_status = '';
			$plugin_frontend_status = '';
			$plugin_backend_status = '';

			foreach ($plugins_configuration as $key => $value) {

				if ($value->controller == $frontend_plugin_name) {

					$plugin_type = (isset($value->type)) ? $value->type : '';
					$plugin_controller = $value->controller;
					$field_identifier = $value->identifier;
					$field_content = $value->field;
					$db_table = (isset($value->dbtable)) ? $value->dbtable : '';
					$plugin_status = $value->plugin_status;
					$plugin_frontend_status = $value->plugin_frontend_status;
					$plugin_backend_status = $value->plugin_backend_status;

					$back_url = [];

					foreach ($_GET as $key => $value) {
						$back_url[$key] = $value;
					}

					$back_url = urlencode(serialize($back_url));

					$context = Context::getContext();
					$id_lang = $context->language->id;

					$this->post_id = Tools::getValue('val_identifier');

					if ($db_table != '') {
						$this->post = JsComposer::getJsControllerValues($db_table, $field_content, $field_identifier, $this->post_id, $id_lang);

						// $this->post = new $plugin_controller((int) $this->post_id);
						$this->post->content = $this->post->$field_content;
					}

				}

			}

		}

		$GLOBALS['post'] = (isset($this->post)) ? $this->post : '';
	}

	function post() {

		!isset($this->post) && $this->setPost();
		return $this->post;
	}

	function renderEditor() {

		$this->post_url = JsComposer::getVccontentanywhereLink((int)(Tools::getValue('id_vccontentanywhere')), null, null, vc_get_cms_lang_id(), null);

		if (!$this->inlineEnabled()) {
			header('Location: ' . $this->post_url);
		}

		self::$admin_body_classes = $this->filterAdminBodyClass('');
		$this->url = $this->post_url . (preg_match('/\?/', $this->post_url) ? '&' : '?') . 'vc_editable=true';

		if (!defined('IFRAME_REQUEST')) {
			define('IFRAME_REQUEST', true);
		}

		options_include_templates();

		$this->render('editor');
	}

	function setEditorTitle($admin_title) {

		return sprintf(__('Edit %s with Visual Composer', 'js_composer'), $this->post_type->labels->singular_name);
	}

	function render($template) {

		vc_include_template('editors/frontend_' . $template . '.tpl.php', ['editor' => $this]);
	}

	function renderEditButton($link) {

		if ($this->showButton()) {
			return $link . ' <a href="' . self::getInlineUrl() . '" id="vc_load-inline-editor" class="vc_inline-link">' . __('Edit with Visual Composer', 'js_composer') . '</a>';
		}

		return $link;
	}

	function renderRowAction($actions) {

		$post = get_post();

		if ($this->showButton($post->ID)) {
			$actions['edit_vc'] = '<a
		href="' . $this->getInlineUrl('', $post->ID) . '">' . __('Edit with Visual Composer', 'js_composer') . '</a>';
		}

		return $actions;
	}

	function showButton($post_id = null) {

		global $current_user;
		get_currentuserinfo();
		$show = true;

		if (!self::inlineEnabled() || !current_user_can('edit_post', $post_id)) {
			return false;
		}

		/** @var $settings - get use group access rules */

		$settings = vc_settings()->get('groups_access_rules');

		foreach ($current_user->roles as $role) {

			if (isset($settings[$role]['show']) && $settings[$role]['show'] === 'no') {
				$show = false;
				break;
			}

		}

		return $show && in_array(get_post_type(), vc_editor_post_types());
	}

	function adminBarEditLink($wp_admin_bar) {

		global $wp_admin_bar;

		if (is_singular()) {

			if ($this->showButton(get_the_ID())) {
				$wp_admin_bar->add_menu([
					// 'parent' => $root_menu,
					'id'    => 'vc_inline-admin-bar-link',
					'title' => __('Edit with Visual Composer', "js_composer"),
					'href'  => self::getInlineUrl(),
					'meta'  => ['class' => 'vc_inline-link'],
				]);
			}

		}

	}

	function setTemplateContent($content) {

		$this->template_content = $content;
	}

	function getTemplateContent() {

		return $this->template_content;
//		return apply_filters( 'vc_inline_template_content', $this->template_content );
	}

	function renderTemplates() {

		$this->render('templates');
		die();
	}

	function loadTinyMceSettings() {

		if (!class_exists('_WP_Editors')) {
			require ABSPATH . WPINC . '/class-wp-editor.php';
		}

		$set = _WP_Editors::parse_settings(self::$content_editor_id, self::$content_editor_settings);
		_WP_Editors::editor_settings(self::$content_editor_id, $set);
	}

	function loadIFrameJsCss() {

		wp_enqueue_script('jquery-ui-tabs');
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script('jquery-ui-droppable');
		wp_enqueue_script('jquery-ui-draggable');
		wp_enqueue_script('jquery-ui-accordion');
		wp_enqueue_script('jquery-ui-autocomplete');
		wp_enqueue_script('wpb_composer_front_js');
		wp_enqueue_style('js_composer_front');
		wp_enqueue_style('vc_inline_css', vc_asset_url('css/js_composer_frontend_editor_iframe.css'));
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script('jquery-ui-draggable');
		wp_enqueue_script('waypoints');
		wp_enqueue_script('wpb_scrollTo_js', vc_asset_url('lib/scrollTo/jquery.scrollTo.min.js'), ['jquery'], WPB_VC_VERSION, true);
		wp_enqueue_style('js_composer_custom_css');

		wp_enqueue_script('wpb_php_js', vc_asset_url('lib/php.default/php.default.min.js'), ['jquery'], WPB_VC_VERSION, true);
		wp_enqueue_script('vc_inline_iframe_js', vc_asset_url('js/frontend_editor/vc_page_editable.js'), ['jquery', 'jquery-ui-sortable', 'jquery-ui-draggable'], WPB_VC_VERSION, true);
		do_action('vc_load_iframe_jscss');
	}

	function loadShortcodes() {

		!defined('CONCATENATE_SCRIPTS') && define('CONCATENATE_SCRIPTS', false);
		$this->setPost();
		$shortcodes = (array) vc_post_param('shortcodes');
		$this->renderShortcodes($shortcodes);
		die();
	}

	function fullUrl($s) {

		$ssl = (!empty($s['HTTPS']) && $s['HTTPS'] == 'on') ? true : false;
		$sp = strtolower($s['SERVER_PROTOCOL']);
		$protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
		$port = $s['SERVER_PORT'];
		$port = ((!$ssl && $port == '80') || ($ssl && $port == '443')) ? '' : ':' . $port;
		$host = (isset($s['HTTP_X_FORWARDED_HOST']) ? $s['HTTP_X_FORWARDED_HOST'] : isset($s['HTTP_HOST'])) ? $s['HTTP_HOST'] : $s['SERVER_NAME'];
		return $protocol . '://' . $host . $port . $s['REQUEST_URI'];
	}

	static function cleanStyle() {

		return '';
	}

	function enqueueRequired() {

		global $wpVC_setup;
		do_action('wp_enqueue_scripts');
		visual_composer()->frontCss();
		visual_composer()->frontJsRegister();
	}

	function renderShortcodes($shortcodes) {

		$output = '';

		foreach ($shortcodes as $shortcode) {

			if (isset($shortcode['id']) && isset($shortcode['string'])) {
				$output .= '<div data-type="element" data-model-id="' . $shortcode['id'] . '">';
				$shortcode_settings = ComposerMap::getShortCode($shortcode['tag']);
				$is_container = (isset($shortcode_settings['is_container']) && $shortcode_settings['is_container'] === true) || (isset($shortcode_settings['as_parent']) && $shortcode_settings['as_parent'] !== false);

				if ($is_container) {
					$shortcode['string'] = preg_replace('/\]/', '][vc_container_anchor]', $shortcode['string'], 1);
				}

				$output .= '<div class="vc_element"' . self::cleanStyle() . ' data-container="' . $is_container . '" data-model-id="' . $shortcode['id'] . '">' . $this->wrapperStart() . JsComposer::do_shortcode(stripslashes($shortcode['string'])) . $this->wrapperEnd() . '</div>';
				$output .= '</div>';
			}

		}

		echo $output;
	}

	function filterAdminBodyClass($string) {

		$string .= (strlen($string) > 0 ? ' ' : '') . 'vc_editor vc_inline-shortcode-edit-form';

		if (vc_settings()->get('not_responsive_css') === '1') {
			$string .= ' vc_responsive_disabled';
		}

		return $string;
	}

	function adminFile($path) {

		return ABSPATH . 'wp-admin/' . $path;
	}

	function enqueueAdmin() {

		wp_enqueue_style('wp-color-picker');
		wp_enqueue_script('wp-color-picker');
		wp_enqueue_style('farbtastic');
		wp_enqueue_style('ui-custom-theme');
		// wp_enqueue_style('isotope-css');
		wp_enqueue_style('animate-css');
		wp_enqueue_style('wpb_jscomposer_autosuggest');
		wp_enqueue_script('jquery-ui-tabs');
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script('jquery-ui-droppable');
		wp_enqueue_script('jquery-ui-draggable');
		wp_enqueue_script('jquery-ui-accordion');
		wp_enqueue_script('jquery-ui-autocomplete');
		wp_enqueue_script('vc_bootstrap_js', vc_asset_url('lib/bootstrap3/dist/js/bootstrap.min.js'), ['jquery'], WPB_VC_VERSION, true);
		wp_enqueue_script('farbtastic');
		// wp_enqueue_script('isotope');
		wp_enqueue_script('wpb_scrollTo_js');
		wp_enqueue_script('wpb_php_js');
		wp_enqueue_script('wpb_js_composer_js_sortable');
		wp_enqueue_script('wpb_json-js');
		wp_enqueue_script('wpb_js_composer_js_tools');
		wp_enqueue_script('wpb_js_composer_js_atts');
		wp_enqueue_script('wpb_jscomposer_media_editor_js');
		wp_enqueue_script('wpb_jscomposer_autosuggest_js');
		wp_enqueue_script('webfont', '//ajax.googleapis.com/ajax/libs/webfont/1.4.7/webfont.js'); // Google Web Font CDN
		wp_enqueue_script('vc_inline_shortcodes_builder_js', vc_asset_url('js/frontend_editor/shortcodes_builder.js'), ['jquery', 'underscore', 'backbone', 'wpb_js_composer_js_tools'], WPB_VC_VERSION, true);
		wp_enqueue_script('vc_inline_models_js', vc_asset_url('js/frontend_editor/models.js'), ['vc_inline_shortcodes_builder_js'], WPB_VC_VERSION, true);
		wp_enqueue_script('vc_inline_panels_js', vc_asset_url('js/editors/panels.js'), ['vc_inline_models_js'], WPB_VC_VERSION, true);
		wp_enqueue_script('vc_inline_js', vc_asset_url('js/frontend_editor/frontend_editor.js'), ['vc_inline_panels_js'], WPB_VC_VERSION, true);
		wp_enqueue_script('vc_inline_custom_view_js', vc_asset_url('js/frontend_editor/custom_views.js'), ['vc_inline_shortcodes_builder_js', 'vc_inline_panels_js'], WPB_VC_VERSION, true);
		wp_enqueue_script('vc_inline_build_js', vc_asset_url('js/frontend_editor/build.js'), ['vc_inline_custom_view_js'], WPB_VC_VERSION, true);
		wp_enqueue_style('vc_inline_css', vc_asset_url('css/js_composer_frontend_editor.css'), [], WPB_VC_VERSION);
		wp_enqueue_script('wpb_ace');
	}

	/**
	 * Enqueue js/css files from mapped shortcodes.
	 *
	 * To add js/css files to this enqueue please add front_enqueue_js/front_enqueue_css setting in vc_map array.
	 */
	function enqueueMappedShortcode() {

		foreach (ComposerMap::getUserShortCodes() as $shortcode) {

			if (!empty($shortcode['front_enqueue_js'])) {
				wp_enqueue_script('front_enqueue_js_' . $shortcode['base'], $shortcode['front_enqueue_js'], ['vc_inline_custom_view_js'], WPB_VC_VERSION, true);
			}

			if (!empty($shortcode['front_enqueue_css'])) {
				wp_enqueue_style('front_enqueue_css_' . $shortcode['base'], $shortcode['front_enqueue_css'], ['vc_inline_css'], WPB_VC_VERSION, 'all');
			}

		}

	}

	function buildEditForm() {

		$element = vc_get_param('element');
		$shortCode = stripslashes(vc_get_param('shortcode'));
		WpbakeryShortcodeParams::setEnqueue(true);
		$this->removeShortCode($element);
		$settings = ComposerMap::getShortCode($element);
		new WPBakeryShortCode_Settings($settings);
		return do_shortcode($shortCode);
	}

	function outputShortcodeSettings($element) {

		echo '<div class="vc_element-settings wpb-edit-form" data-id="' . $element['id'] . '">';
		$shortCode = stripslashes($element['shortcode']);
		$this->removeShortCode($element['tag']);
		$settings = ComposerMap::getShortCode($element['tag']);
		new WPBakeryShortCode_Settings($settings);
		echo do_shortcode($shortCode);
		echo '</div>';
	}

	function getPageShortcodes() {

		$post = $this->post();

		if (!is_object($post)) {
			$post = (object) $post;
		}

		$content = $post->content;

//                $content = preg_replace_callback('/^\<p\>|(.*)\<\/?p\>$/', create_function('$m', 'return $m[1];'), $content[vc_get_cms_lang_id()]);
		$content = $content[vc_get_cms_lang_id()];

		$not_shortcodes = preg_split('/' . self::shortcodesRegexp() . '/', $content);

		foreach ($not_shortcodes as $string) {

			if (strlen(trim($string)) > 0) {
				$content = preg_replace("/(" . preg_quote($string, '/') . "(?!\[\/))/", '[vc_row][vc_column width="1/1"][vc_column_text]$1[/vc_column_text][/vc_column][/vc_row]', $content);
			}

		}

		$pattern = ['/\<script([^\>]*)\>/', '/\<\/script([^\>]*)\>/'];
		$replace = ['<style$1>/** vc_js-placeholder **/', '</style$1><!-- vc_js-placeholder -->'];
		echo preg_replace($pattern, $replace, $this->parseShortcodesString($content));

	}

	function getTemplateShortcodes() {

		$template_id = vc_post_param('template_id');

		if (!isset($template_id) || $template_id == "") {
			echo 'Error: TPL-02';
			die();
		}

		$option_name = 'wpb_js_templates';
		$saved_templates = get_option($option_name);

		$content = isset($saved_templates[$template_id]) ? $saved_templates[$template_id]['template'] : '';
		echo $this->parseShortcodesString($content);
	}

	function parseShortcodesString($content, $is_container = false, $parent_id = false) {

		$string = '';

		preg_match_all('/' . self::shortcodesRegexp() . '/', $content, $found);

		if (count($found[2]) == 0) {
			return $is_container && strlen($content) > 0 ? $this->parseShortcodesString('[vc_column_text]' . $content . '[/vc_column_text]', false, $parent_id) : $content;
			return $content;
		}

		foreach ($found[2] as $index => $s) {
			$id = md5(time() . '-' . $this->tag_index++);
			$content = $found[5][$index];
			$shortcode = ['tag' => $s, 'attrs_query' => $found[3][$index], 'attrs' => JsComposer::shortcode_parse_atts($found[3][$index]), 'id' => $id, 'parent_id' => $parent_id];

			if (ComposerMap::getParam($s, 'content') !== false) {
				$shortcode['attrs']['content'] = $content;
			}

			$this->post_shortcodes[] = $shortcode;

			$string .= $this->toString($shortcode, $content);
		}

		return $string;
	}

	function toString($shortcode, $content) {

		$shortcode_settings = ComposerMap::getShortCode($shortcode['tag']);
		$is_container = (isset($shortcode_settings['is_container']) && $shortcode_settings['is_container'] === true) || (isset($shortcode_settings['as_parent']) && $shortcode_settings['as_parent'] !== false);

		return JsComposer::do_shortcode('<div class="vc_element" data-tag="' . $shortcode['tag'] . '" data-model-id="' . $shortcode['id'] . '"' . self::cleanStyle() . '>' . $this->wrapperStart()
			. '[' . $shortcode['tag'] . ' ' . $shortcode['attrs_query'] . ']' . ($is_container ? '[vc_container_anchor]' : '') . $this->parseShortcodesString($content, $is_container, $shortcode['id']) . '[/' . $shortcode['tag'] . ']' . $this->wrapperEnd() . '</div>');
	}

}

if (!function_exists('vc_container_anchor')) {
	function vc_container_anchor() {

		return '<span class="vc_container-anchor"></span>';
	}

}
