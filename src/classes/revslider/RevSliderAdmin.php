<?php

class RevSliderAdmin extends RevSliderFunctionsAdmin {

	// private $theme_mode = false;
	private $view = 'slider';
	private $user_role = 'admin';
	private $global_settings = [];
	private $screens = []; // holds all RevSlider Relevant screens in it
	private $allowed_views = ['sliders', 'slider', 'slide', 'update']; // holds pages, that are allowed to be included
	private $pages = ['revslider']; // , 'revslider_navigation', 'rev_addon', 'revslider_global_settings'
	private $path_views;
	public $show_content = null;

	public function __construct($content = true) {

		$this->path_views = RS_PLUGIN_PATH . 'admin/views/';
		$this->global_settings = $this->get_global_settings();

		$this->set_current_page();
		$this->set_user_role();

		$this->add_actions();

		if ($content == false) {
			return;
		}

		$this->processAdmin();

	}

	public function processAdmin() {

		RevLoader::loadAllAddons();

		if (!RevLoader::is_ajax()) {
			$this->display_admin_page();
		}

	}

	/**
	 * enqueue all admin styles
	 **/
	public function enqueue_admin_styles() {

		if (in_array($this->get_val($_GET, 'page'), $this->pages)) {

			if (RevLoader::is_rtl()) {

			}

		}

	}

	/**
	 * enqueue all admin scripts
	 **/
	public function enqueue_admin_scripts() {

		if (in_array($this->get_val($_GET, 'page'), $this->pages)) {
			global $wp_scripts;
			$view = $this->get_val($_GET, 'view');

			$wait_for = ['media-editor', 'media-audiovideo'];

			if (RevLoader::is_admin()) {
				$wait_for[] = 'mce-view';
				$wait_for[] = 'image-edit';
			}

			$wait_for = [];

		}

	}

	public function set_user_role() {

		$this->user_role = $this->get_val($this->global_settings, 'permission', 'admin');
	}

	/**
	 * Add Classes to the WordPress body
	 *
	 * @since 6.0
	 */
	function modify_admin_body_class($classes) {

		$classes .= ($this->get_val($_GET, 'page') == 'revslider' && $this->get_val($_GET, 'view') == 'slide') ? ' rs-builder-mode' : '';
		$classes .= ($this->_truefalse($this->get_val($this->global_settings, 'highContrast', false)) === true && $this->get_val($_GET, 'page') === 'revslider') ? ' rs-high-contrast' : '';

		return $classes;
	}

	/**
	 * Add all actions that the backend needs here
	 **/
	public function add_actions() {

		RevLoader::add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_styles']);
		RevLoader::add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
		RevLoader::add_action('wp_ajax_revslider_ajax_action', [$this, 'do_ajax_action']); // ajax response to save slider options.

	}

	/**
	 * Add all filters that the backend needs here
	 **/
	public function add_filters() {

		RevLoader::add_filter('admin_body_class', [$this, 'modify_admin_body_class']);
		RevLoader::add_filter('plugin_locale', [$this, 'change_lang'], 10, 2);
	}

	/**
	 * Change the language of the Sldier Backend even if WordPress is set to be a different language
	 *
	 * @since: 6.1.6
	 **/
	public function change_lang($locale, $domain = '') {

		return (in_array($domain, ['revslider', 'revsliderhelp'], true)) ? $this->get_val($this->global_settings, 'lang', 'default') : $locale;
	}

	/**
	 * add addon merged notices
	 *
	 * @since: 6.2.0
	 **/
	public function add_addon_plugins_page_notices() {

		?>
<div class="error below-h2 soc-notice-wrap revaddon-notice" style="display: none;">
    <p><?php echo $this->l('Action required for Slider Revolution AddOns: Please <a href="https://classydevs.com/docs/slider-revolution-6-phenyxshop/quick-setup/install-activate/" target="_blank">install</a>/<a href="https://classydevs.com/docs/slider-revolution-6-phenyxshop/quick-setup/register-plugin-2" target="_blank">activate</a>/<a href="https://classydevs.com/docs/slider-revolution-6-phenyxshop/quick-setup/register-plugin/" target="_blank">update</a> Slider Revolution</a>'); ?><span
            data-addon="rs-addon-notice" data-noticeid="rs-addon-merged-notices" style="float: right; cursor: pointer"
            class="revaddon-dismiss-notice dashicons dashicons-dismiss"></span></p>
</div>
<?php
}

	/**
	 * Show message for activation benefits
	 **/
	public static function show_purchase_notice($plugin_file, $plugin_data, $plugin_status) {

		?>
<p>
    <?php $this->l('Activate Slider Revolution for <a href="https://classydevs.com/slider-revolution-phenyxshop/" target="_blank">Premium Benefits (e.g. Live Updates)</a>.');?>
</p>
<?php
}

	/**
	 * Return the default suggested privacy policy content.
	 *
	 * @return string The default policy content.
	 */
	public function get_default_privacy_content() {

		return $this->l(
			'<h2>In case you’re using Google Web Fonts (default) or playing videos or sounds via YouTube or Vimeo in Slider Revolution we recommend to add the corresponding text phrase to your privacy police:</h2>
		<h3>YouTube</h3> <p>Our website uses plugins from YouTube, which is operated by Google. The operator of the pages is YouTube LLC, 901 Cherry Ave., San Bruno, CA 94066, USA.</p> <p>If you visit one of our pages featuring a YouTube plugin, a connection to the YouTube servers is established. Here the YouTube server is informed about which of our pages you have visited.</p> <p>If you\'re logged in to your YouTube account, YouTube allows you to associate your browsing behavior directly with your personal profile. You can prevent this by logging out of your YouTube account.</p> <p>YouTube is used to help make our website appealing. This constitutes a justified interest pursuant to Art. 6 (1) (f) DSGVO.</p> <p>Further information about handling user data, can be found in the data protection declaration of YouTube under <a href="https://www.google.de/intl/de/policies/privacy" target="_blank">https://www.google.de/intl/de/policies/privacy</a>.</p>
		<h3>Vimeo</h3> <p>Our website uses features provided by the Vimeo video portal. This service is provided by Vimeo Inc., 555 West 18th Street, New York, New York 10011, USA.</p> <p>If you visit one of our pages featuring a Vimeo plugin, a connection to the Vimeo servers is established. Here the Vimeo server is informed about which of our pages you have visited. In addition, Vimeo will receive your IP address. This also applies if you are not logged in to Vimeo when you visit our plugin or do not have a Vimeo account. The information is transmitted to a Vimeo server in the US, where it is stored.</p> <p>If you are logged in to your Vimeo account, Vimeo allows you to associate your browsing behavior directly with your personal profile. You can prevent this by logging out of your Vimeo account.</p> <p>For more information on how to handle user data, please refer to the Vimeo Privacy Policy at <a href="https://vimeo.com/privacy" target="_blank">https://vimeo.com/privacy</a>.</p>
		<h3>Google Web Fonts</h3> <p>For uniform representation of fonts, this page uses web fonts provided by Google. When you open a page, your browser loads the required web fonts into your browser cache to display texts and fonts correctly.</p> <p>For this purpose your browser has to establish a direct connection to Google servers. Google thus becomes aware that our web page was accessed via your IP address. The use of Google Web fonts is done in the interest of a uniform and attractive presentation of our plugin. This constitutes a justified interest pursuant to Art. 6 (1) (f) DSGVO.</p> <p>If your browser does not support web fonts, a standard font is used by your computer.</p> <p>Further information about handling user data, can be found at <a href="https://developers.google.com/fonts/faq" target="_blank">https://developers.google.com/fonts/faq</a> and in Google\'s privacy policy at <a href="https://www.google.com/policies/privacy/" target="_blank">https://www.google.com/policies/privacy/</a>.</p>
		<h3>SoundCloud</h3><p>On our pages, plugins of the SoundCloud social network (SoundCloud Limited, Berners House, 47-48 Berners Street, London W1T 3NF, UK) may be integrated. The SoundCloud plugins can be recognized by the SoundCloud logo on our site.</p>
		<p>When you visit our site, a direct connection between your browser and the SoundCloud server is established via the plugin. This enables SoundCloud to receive information that you have visited our site from your IP address. If you click on the “Like” or “Share” buttons while you are logged into your SoundCloud account, you can link the content of our pages to your SoundCloud profile. This means that SoundCloud can associate visits to our pages with your user account. We would like to point out that, as the provider of these pages, we have no knowledge of the content of the data transmitted or how it will be used by SoundCloud. For more information on SoundCloud’s privacy policy, please go to https://soundcloud.com/pages/privacy.</p><p>If you do not want SoundCloud to associate your visit to our site with your SoundCloud account, please log out of your SoundCloud account.</p>'
		);
	}

	/**
	 * The Ajax Action part for backend actions only
	 **/
	public function do_ajax_action() {

		$slider = new RevSliderSlider();
		$slide = new RevSliderSlide();

		$action = Tools::getValue('client_action');
		$data = Tools::getValue('data');
		$data = ($data == '') ? [] : $data;
		$nonce = Tools::getValue('nonce');
		$nonce = (empty($nonce)) ? Tools::getValue('rs-nonce') : $nonce;
		try {

			if (RS_DEMO) {

				switch ($action) {
				case 'get_template_information_short':
				case 'import_template_slider':
				case 'install_template_slider':
				case 'install_template_slide':
				case 'get_list_of':
				case 'get_global_settings':
				case 'get_full_slider_object':
				case 'check_system':
				case 'load_plugin':
				case 'get_layers_by_slide':
				case 'silent_slider_update':
				case 'get_help_directory':
				case 'set_tooltip_preference':
				case 'load_builder':
				case 'load_library_object':
				case 'get_tooltips':
					// case 'preview_slider':
					// these are all okay in demo mode
					break;
				default:
					$this->ajax_response_error($this->l('Function Not Available in Demo Mode'));
					exit;
					break;
				}

			}

			switch ($action) {

			
			case 'add_custom_hook':

				$hookname = trim($this->get_val($data, 'hookname'));

				if (!isset($hookname) || $hookname == "") {
					$this->ajax_response_error($this->l('You must put a hookname.'));
					break;
				}

				$existing_custom_hooks = RevLoader::get_option('revslider-custom-hooks');
				$existing_custom_hooks = Tools::jsonDecode($existing_custom_hooks, true);

				if (isset($existing_custom_hooks)) {

					if (is_array($existing_custom_hooks)) {

						if (in_array($hookname, $existing_custom_hooks)) {
							$this->ajax_response_error($this->l('This hook is already added!!!'));
							break;
						}

						$existing_custom_hooks[$hookname] = $hookname;
					}

				} else {
					$existing_custom_hooks = [];
					$existing_custom_hooks[$hookname] = $hookname;
				}

				RevLoader::update_option('revslider-custom-hooks', Tools::jsonEncode($existing_custom_hooks));
				$this->ajax_response_success($this->l('Hook added succesfully!!!'));
				break;
			case 'dismiss_dynamic_notice':
				$ids = $this->get_val($data, 'id', []);
				$notices_discarded = RevLoader::get_option('revslider-notices-dc', []);

				if (!empty($ids)) {

					foreach ($ids as $_id) {
						$notices_discarded[] = RevLoader::esc_attr(trim($_id));
					}

					RevLoader::update_option('revslider-notices-dc', $notices_discarded);
				}

				$this->ajax_response_success($this->l('Saved'));
				break;

			case 'get_template_information_short':
				$templates = new RevSliderTemplate();
				$sliders = $templates->get_tp_template_sliders();

				$this->ajax_response_data(['templates' => $sliders]);
				break;

			case 'import_template_slider': // before: import_slider_template_slidersview
				$uid = $this->get_val($data, 'uid');
				$install = $this->get_val($data, 'install', true);
				$templates = new RevSliderTemplate();
				$filepath = $templates->_download_template($uid);

				if ($filepath !== false) {
					$templates->remove_old_template($uid);
					$slider = new RevSliderSliderImport();
					$return = $slider->import_slider(false, $filepath, $uid, false, true, $install);

					if ($this->get_val($return, 'success') == true) {
						$new_id = $this->get_val($return, 'sliderID');

						if ((int) ($new_id) > 0) {
							$map = $this->get_val($return, 'map', []);
							$folder_id = $this->get_val($data, 'folderid', -1);

							if ((int) ($folder_id) > 0) {
								$folder = new RevSliderFolder();
								$folder->add_slider_to_folder($new_id, $folder_id, false);
							}

							$new_slider = new RevSliderSlider();
							$new_slider->init_by_id($new_id);
							$data = $new_slider->get_overview_data();

							$hiddensliderid = $templates->get_slider_id_by_uid($uid);

							$templates->_delete_template($uid); // delete template file

							$this->ajax_response_data(
								[
									'slider'         => $data,
									'hiddensliderid' => $hiddensliderid,
									'map'            => $map,
									'uid'            => $uid,
								]
							);
						}

					}

					$templates->_delete_template($uid); // delete template file

					$error = ($this->get_val($return, 'error') !== '') ? $this->get_val($return, 'error') : $this->l('Slider Import Failed');
					$this->ajax_response_error($error);
				}

				$this->ajax_response_error($this->l('Template Slider Import Failed'));
				break;
			case 'install_template_slider':
				$id = $this->get_val($data, 'sliderid');
				$new_id = $slider->duplicate_slider_by_id($id, true);

				if ((int) ($new_id) > 0) {
					$new_slider = new RevSliderSlider();
					$new_slider->init_by_id($new_id);
					$data = $new_slider->get_overview_data();
					$slide_maps = $slider->get_map();
					$map = [
						'slider' => ['template_to_duplication' => [$id => $new_id]],
						'slides' => $slide_maps,
					];
					$this->ajax_response_data(
						[
							'slider'         => $data,
							'hiddensliderid' => $id,
							'map'            => $map,
						]
					);
				}

				$this->ajax_response_error($this->l('Template Slider Installation Failed'));
				break;
			case 'install_template_slide':
				$template = new RevSliderTemplate();
				$slider_id = (int) ($this->get_val($data, 'slider_id'));
				$slide_id = (int) ($this->get_val($data, 'slide_id'));

				if ($slider_id == 0 || $slide_id == 0) {
				} else {
					$new_slide_id = $slide->duplicate_slide_by_id($slide_id, $slider_id);

					if ($new_slide_id !== false) {
						$slide->init_by_id($new_slide_id);
						$_slides[] = [
							'order'  => $slide->get_order(),
							'params' => $slide->get_params(),
							'layers' => $slide->get_layers(),
							'id'     => $slide->get_id(),
						];

						$this->ajax_response_data(['slides' => $_slides]);
					}

				}

				$this->ajax_response_error($this->l('Slide duplication failed'));
				break;
			case 'import_slider':
				$import = new RevSliderSliderImport();
				$return = $import->import_slider();

				if ($this->get_val($return, 'success') == true) {
					$new_id = $this->get_val($return, 'sliderID');

					if ((int) ($new_id) > 0) {
						$folder = new RevSliderFolder();
						$folder_id = $this->get_val($data, 'folderid', -1);

						if ((int) ($folder_id) > 0) {
							$folder->add_slider_to_folder($new_id, $folder_id, false);
						}

						$new_slider = new RevSliderSlider($new_id);
						$data = $new_slider->get_overview_data();

						$this->ajax_response_data(
							[
								'slider'         => $data,
								'hiddensliderid' => $new_id,
							]
						);
					}

				}

				$error = ($this->get_val($return, 'error') !== '') ? $this->get_val($return, 'error') : $this->l('Slider Import Failed');

				$this->ajax_response_error($error);
				break;
			case 'add_to_media_library':
				$this->ajax_response_data(['media_library' => 'No need to this media.']);

				break;
			case 'adjust_modal_ids':
				$map = $this->get_val($data, 'map', []);

				if (!empty($map)) {
					$slider_map = [];
					$slider_ids = $this->get_val($map, 'slider_map', []);
					$slides_ids = $this->get_val($map, 'slides_map', []);

					$ztt = $this->get_val($slider_ids, 'zip_to_template', []);
					$ztd = $this->get_val($slider_ids, 'zip_to_duplication', []);
					$ttd = $this->get_val($slider_ids, 'template_to_duplication', []);
					$s_a = [];

					if (!empty($slides_ids)) {

						foreach ($slides_ids as $k => $v) {

							if (is_array($v)) {

								foreach ($v as $vk => $vv) {
									$s_a[$vk] = $vv;
								}

								unset($slides_ids[$k]);
							}

						}

					}

					if (!empty($ztt)) {

						foreach ($ztt as $old => $new) {
							$slider = new RevSliderSliderImport();
							$slider->init_by_id($new);

							$slider->update_modal_ids($ztt, $slides_ids);
						}

					}

					if (!empty($ztd)) {

						foreach ($ztd as $old => $new) {
							$slider = new RevSliderSliderImport();
							$slider->init_by_id($new);
							$slider->update_modal_ids($ztd, $s_a);
						}

					}

					if (!empty($ttd)) {

						foreach ($ttd as $old => $new) {
							$slider = new RevSliderSliderImport();
							$slider->init_by_id($new);
							$slider->update_modal_ids($ttd, $slides_ids);
						}

					}

					$this->ajax_response_data([]);
				} else {
					$this->ajax_response_error($this->l('Slider Map Empty'));
				}

				break;
			case 'adjust_js_css_ids':
				$map = $this->get_val($data, 'map', []);

				if (!empty($map)) {
					$slider_map = [];

					foreach ($map as $m) {
						$slider_ids = $this->get_val($m, 'slider_map', []);

						if (!empty($slider_ids)) {

							foreach ($slider_ids as $old => $new) {
								$slider = new RevSliderSliderImport();
								$slider->init_by_id($new);

								$slider_map[] = $slider;
							}

						}

					}

					if (!empty($slider_map)) {

						foreach ($slider_map as $slider) {

							foreach ($map as $m) {
								$slider_ids = $this->get_val($m, 'slider_map', []);
								$slide_ids = $this->get_val($m, 'slide_map', []);

								if (!empty($slider_ids)) {

									foreach ($slider_ids as $old => $new) {
										$slider->update_css_and_javascript_ids($old, $new, $slide_ids);
									}

								}

							}

						}

					}

				}

				break;
			case 'export_slider':
                $id = (int) ($this->get_request_var('id'));
				$export = new RevSliderSliderExport($id);
				
				$return = $export->export_slider($id);
				// will never be called if all is good
				$this->ajax_response_data($return);
				break;
			case 'export_slider_html':
                $id = (int) ($this->get_request_var('id'));
				$export = new RevSliderSliderExportHtml($id);
				
				$return = $export->export_slider_html($id);

				// will never be called if all is good
				$this->ajax_response_data($return);
				break;
			case 'delete_slider':
				$id = $this->get_val($data, 'id');
				$slider = new RevSliderSlider($id);
				$result = $slider->delete();
				$this->ajax_response_success($this->l('Slider Deleted'));
				break;
			case 'duplicate_slider':
				$id = $this->get_val($data, 'id');
				$new_id = $slider->duplicate_slider_by_id($id);

				if ((int) ($new_id) > 0) {
					$new_slider = new RevSliderSlider($new_id);
					//$new_slider->init_by_id($new_id);
					$data = $new_slider->get_overview_data();
					$this->ajax_response_data(['slider' => $data]);
				}

				$this->ajax_response_error($this->l('Duplication Failed'));
				break;
			case 'save_slide':
                $slide_id = $data['slide_id'];
                $slide = new RevSliderSlide($slide_id);
                $params = $this->get_val($data, 'params', []);
		        $params = $this->json_decode_slashes($params);
		        $settings = $this->get_val($data, 'settings', []);
		        $settings = $this->json_decode_slashes($settings);
		        $settings['version'] = $this->get_val($params, 'version', $this->get_val($settings, 'version', RS_REVISION));
                $slide->settings = Tools::jsonEncode($settings);

		        if (isset($params['version'])) {
                    unset($params['version']);
		        }
                $slide->params = Tools::jsonEncode($params);

		        $layers = $this->get_val($data, 'layers', []);
                $layers = $this->json_decode_slashes($layers);
                $layers = (empty($layers) || !is_array($layers)) ? [] : $layers;
		        $slide->layers = Tools::jsonEncode($layers);
                
				$return = $slide->update();

				if ($return) {
					$this->ajax_response_success($this->l('Slide Saved'));
				} else {
					$this->ajax_response_error($this->l('Slide not found'));
				}

				break;
			case 'save_slide_advanced':
				$slide_id = $this->get_val($data, 'slide_id');
				$slider_id = $this->get_val($data, 'slider_id');
				$return = $slide->save_slide_advanced($slide_id, $data, $slider_id);

				if ($return) {
					$this->ajax_response_success($this->l('Slide Saved'));
				} else {
					$this->ajax_response_error($this->l('Slide not found'));
				}

				break;
			case 'save_slider':
				$slider_id = $this->get_val($data, 'slider_id');
                $slider = new RevSliderSlider($slider_id);
                $params = $this->get_val($data, 'params');
		        $params = $this->json_decode_slashes($params);
		        $settings = $this->get_val($data, 'settings');
		        $settings = $this->json_decode_slashes($settings);
		        $settings['version'] = $this->get_val($params, 'version', $this->get_val($settings, 'version'));

		        $title = RevLoader::sanitize_text_field($this->get_val($params, 'title'));
		        $alias = RevLoader::sanitize_text_field($this->get_val($params, 'alias'));

		        $this->validate_not_empty($title, 'Title');
		        $this->validate_not_empty($alias, 'Alias');

		        if (empty($slider_id) && $this->check_alias($alias)) {
			        $this->throw_error($this->l('A Slider with the given alias already exists'));
		        }
                $slider->title = $title;
		        $slider->alias = $alias;
		        $slider->params = Tools::jsonEncode($params);
		        $slider->settings = Tools::jsonEncode($settings);
                $return = $slider->update();
               
				$mod_obj = Plugin::getInstanceByName('revslider');

				if (isset($slider_params['layout']['displayhook']) && $slider_params['layout']['displayhook'] != '') {
					$mod_obj->registerHook($slider_params['layout']['displayhook']);
				}
				
				$missing_slides = [];
				$delete_slides = [];

				if ($return !== false) {
                    $slide_ids = $this->get_val($data, 'slide_ids', []);
					if (!empty($slide_ids)) {
						$slides = $slider->get_slides(false, true);
						// get the missing Slides (if any at all)

						foreach ($slide_ids as $slide_id) {
							$found = false;
                            $slide = new RevSliderSlide($slide_id);
							foreach ($slides as $_slide) {

								if ($_slide->id !== $slide->id) {
									continue;
								}

								$found = true;
							}

							if (!$found) {
								$missing_slides[] = $slide_id;
							}

						}

						// get the Slides that are no longer needed and delete them

						foreach ($slides as $key => $_slide) {
							$id = $_slide->id;

							if (!in_array($id, $slide_ids)) {
								$delete_slides[] = $id;
								unset($slides[$key]); // remove none existing slides for further ordering process
							}

						}

						if (!empty($delete_slides)) {

							foreach ($delete_slides as $delete_slide) {
								$slide->delete();
							}

						}

						// change the order of slides

						foreach ($slides as $order => $_slide) {
							$_slide->slide_order = $order + 1;
                            $_slide->params = Tools::jsonEncode($_slide->params);
                            $_slide->layers = Tools::jsonEncode($_slide->layers);
                            $_slide->settings = Tools::jsonEncode($_slide->settings);
                            $_slide->update();
						}

					}

					$this->ajax_response_data(
						[
							'missing' => $missing_slides,
							'delete'  => $delete_slides,
						]
					);
				} else {
					$this->ajax_response_error($this->l('Slider not found'));
				}

				break;
			case 'delete_slide':
				$slide_id = (int) ($this->get_val($data, 'slide_id', ''));
				$return = ($slide_id > 0) ? $slide->delete_slide_by_id($slide_id) : false;

				if ($return !== false) {
					$this->ajax_response_success($this->l('Slide deleted'));
				} else {
					$this->ajax_response_error($this->l('Slide could not be deleted'));
				}

				break;
			case 'duplicate_slide':
				$slide_id = (int) ($this->get_val($data, 'slide_id', ''));
				$slider_id = (int) ($this->get_val($data, 'slider_id', ''));

				$new_slide_id = $slide->duplicate_slide_by_id($slide_id, $slider_id);

				if ($new_slide_id !== false) {
					$slide= new RevSliderSlide($new_slide_id);
					$_slide = $slide->get_overview_data();

					$this->ajax_response_data(['slide' => $_slide]);
				} else {
					$this->ajax_response_error($this->l('Slide could not duplicated'));
				}

				break;
			case 'update_slide_order':
				$slide_ids = $this->get_val($data, 'slide_ids', []);

				// change the order of slides

				if (!empty($slide_ids)) {

					foreach ($slide_ids as $order => $id) {
                        $slide= new RevSliderSlide($id);
                        $new_order = $order + 1;
                        $slide->slide_order = $new_order;
		                $slide->update();
						
					}

					$this->ajax_response_success($this->l('Slide order changed'));
				} else {
					$this->ajax_response_error($this->l('Slide order could not be changed'));
				}

				break;
			case 'getSliderImage':
				// Available Sliders
				$slider = new RevSliderSlider();
				$arrSliders = $slider->get_sliders();
				$post60 = (version_compare($slider->get_setting('version', '1.0.0'), '6.0.0', '<')) ? false : true;
				// Given Alias
				$alias = $this->get_val($data, 'alias');
				$return = array_search($alias, $arrSliders);

				foreach ($arrSliders as $sliderony) {

					if ($sliderony->get_alias() == $alias) {
						$slider_found = $sliderony->get_overview_data();
						$return = $slider_found['bg']['src'];
						$title = $slider_found['title'];
					}

				}

				if (!$return) {
					$return = '';
				}

				if (!empty($title)) {
					$this->ajax_response_data(
						[
							'image' => $return,
							'title' => $title,
						]
					);
				} else {
					$this->ajax_response_error($this->l('The Slider with the alias "' . $alias . '" is not available!'));
				}

				break;
			case 'getSliderSizeLayout':
				// Available Sliders
				$slider = new RevSliderSlider();
				$arrSliders = $slider->get_sliders();
				$post60 = (version_compare($slider->get_setting('version', '1.0.0'), '6.0.0', '<')) ? false : true;
				// Given Alias
				$alias = $this->get_val($data, 'alias');

				$return = array_search($alias, $arrSliders);

				foreach ($arrSliders as $sliderony) {

					if ($sliderony->get_alias() == $alias) {
						$slider_found = $sliderony->get_overview_data();
						$return = $slider_found['size'];
						$title = $slider_found['title'];
					}

				}

				$this->ajax_response_data(
					[
						'layout' => $return,
						'title'  => $title,
					]
				);
				break;
			case 'get_list_of':
				$type = $this->get_val($data, 'type');

				switch ($type) {
				case 'sliders':
					$slider = new RevSliderSlider();
					$arrSliders = $slider->get_sliders();
					$return = [];

					foreach ($arrSliders as $sliderony) {
						$return[$sliderony->get_id()] = [
							'slug'    => $sliderony->get_alias(),
							'title'   => $sliderony->get_title(),
							'type'    => $sliderony->get_type(),
							'subtype' => $sliderony->get_param(['source', 'post', 'subType'], false),
						];
					}

					$this->ajax_response_data(['sliders' => $return]);
					break;

				case 'pages':

					$return = [];
					$this->ajax_response_data(['pages' => $return]);
					break;
				case 'posttypes':
					$this->ajax_response_data(['posttypes' => 'No need to posttypes']);

					break;
				}

				break;
			
			case 'get_global_settings':
				$this->ajax_response_data(['global_settings' => $this->global_settings]);
				break;
			case 'update_global_settings':
				$global = $this->get_val($data, 'global_settings', []);

				if (!empty($global)) {
					$return = $this->set_global_settings($global);

					if ($return === true) {
						$this->ajax_response_success($this->l('Global Settings saved/updated'));
					} else {
						$this->ajax_response_error($this->l('Global Settings not saved/updated'));
					}

				} else {
					$this->ajax_response_error($this->l('Global Settings not saved/updated'));
				}

				break;
			case 'create_navigation_preset':
				$nav = new RevSliderNavigation();
				$return = $nav->add_preset($data);

				if ($return === true) {
					$this->ajax_response_success($this->l('Navigation preset saved/updated'), ['navs' => $nav->get_all_navigations_builder()]);
				} else {

					if ($return === false) {
						$return = $this->l('Preset could not be saved/values are the same');
					}

					$this->ajax_response_error($return);
				}

				break;
			case 'delete_navigation_preset':
				$nav = new RevSliderNavigation();
				$return = $nav->delete_preset($data);

				if ($return === true) {
					$this->ajax_response_success($this->l('Navigation preset deleted'), ['navs' => $nav->get_all_navigations_builder()]);
				} else {

					if ($return === false) {
						$return = $this->l('Preset not found');
					}

					$this->ajax_response_error($return);
				}

				break;
			case 'save_navigation': // also deletes if requested
				$_nav = new RevSliderNavigation();
				$navs = (array) $this->get_val($data, 'navs', []);
				$delete_navs = (array) $this->get_val($data, 'delete', []);

				if (!empty($delete_navs)) {

					foreach ($delete_navs as $dnav) {
						$_nav->delete_navigation($dnav);
					}

				}

				if (!empty($navs)) {
					$_nav->create_update_full_navigation($navs);
				}

				$navigations = $_nav->get_all_navigations_builder();

				$this->ajax_response_data(['navs' => $navigations]);
				break;
			case 'delete_animation':
				$animation_id = $this->get_val($data, 'id');
				$admin = new RevSliderFunctionsAdmin();
				$return = $admin->delete_animation($animation_id);

				if ($return) {
					$this->ajax_response_success($this->l('Animation deleted'));
				} else {
					$this->ajax_response_error($this->l('Deletion failed'));
				}

				break;
			case 'save_animation':
				$admin = new RevSliderFunctionsAdmin();
				$id = $this->get_val($data, 'id', false);
				$type = $this->get_val($data, 'type', 'in');
				$animation = $this->get_val($data, 'obj');

				if ($id !== false) {
					$return = $admin->update_animation($id, $animation, $type);
				} else {
					$return = $admin->insert_animation($animation, $type);
				}

				if ((int) ($return) > 0) {
					$this->ajax_response_data(['id' => $return]);
				} else

				if ($return === true) {
					$this->ajax_response_success($this->l('Animation saved'));
				} else {

					if ($return == false) {
						$this->ajax_response_error($this->l('Animation could not be saved'));
					}

					$this->ajax_response_error($return);
				}

				break;
			case 'get_slides_by_slider_id':
				$sid = (int) ($this->get_val($data, 'id'));
				$slides = [];
				$_slides = $slide->get_slides_by_slider_id($sid);

				if (!empty($_slides)) {

					foreach ($_slides as $slide) {
						$slides[] = $slide->get_overview_data();
					}

				}

				$this->ajax_response_data(['slides' => $slides]);
				break;
			case 'get_full_slider_object':
				
				$slide_id = $this->get_val($data, 'id');

				$slide_id = RevSliderFunction::esc_attr_deep($slide_id);
				$slider_alias = $this->get_val($data, 'alias', '');
				$slider_alias = RevSliderFunction::esc_attr_deep($slider_alias);

				
				if ($slider_alias !== '') {
					$slider_id = $slider->init_by_alias($slider_alias);
				} else {

					if (strpos($slide_id, 'slider-') !== false) {
						$slider_id = str_replace('slider-', '', $slide_id);
					} else {
						$slider_id = $slider->init_by_id($slide_id);

						if ((int) ($slider_id) == 0) {

							$this->ajax_response_error($this->l('Slider could not be loaded'));
						}

					}

					$slider = new RevSliderSlider($slider_id);
				}

				if ($slider->inited === false) {

					$this->ajax_response_error($this->l('Slider could not be loaded'));
				}

				// create static Slide if the Slider not yet has one
				$static_slide_id = $slide->get_static_slide_id($slider->id);
				$static_slide_id = ((int) ($static_slide_id) === 0) ? $slide->create_slide($slider->id, '', true) : $static_slide_id;

				$static_slide = false;

				if ((int) ($static_slide_id) > 0) {
					$static_slide = new RevSliderStaticSlide($static_slide_id);

				}

				$slides = $slider->get_slides(false, true);
				$_slides = [];
				$_static_slide = [];

				if (!empty($slides)) {

					foreach ($slides as $s) {
						$_slides[] = [
							'order'  => $s->slide_order,
							'params' => $s->params,
							'layers' => $s->layers,
							'id'     => $s->id,
						];
					}

				}

				if (!empty($static_slide)) {
					$_static_slide = [
						'params' => $static_slide->params,
						'layers' => $static_slide->layers,
						'id'     => $static_slide->id,
					];
				}

				$obj = [
					'id'              => $slider_id,
					'alias'           => $slider->alias,
					'title'           => $slider->title,
					'slider_params'   => $slider->params,
					'slider_settings' => $slider->settings,
					'slides'          => $_slides,
					'static_slide'    => $_static_slide,
				];

				$this->ajax_response_data($obj);
				break;
			case 'load_builder':
				ob_start();
				include_once RS_PLUGIN_PATH . 'admin/views/builder.php';
				$builder = ob_get_contents();
				ob_clean();
				ob_end_clean();

				$this->ajax_response_data($builder);
				break;
			case 'create_slider_folder':
				$folder = new RevSliderFolder();
				$title = $this->get_val($data, 'title', $this->l('New Folder'));
				$parent = $this->get_val($data, 'parentFolder', 0);
				$new = $folder->create_folder($title, $parent);

				if ($new !== false) {
					$overview_data = $new->get_overview_data();
					$this->ajax_response_data(['folder' => $overview_data]);
				} else {
					$this->ajax_response_error($this->l('Folder Creation Failed'));
				}

				break;
			case 'delete_slider_folder':
				$id = $this->get_val($data, 'id');
				$folder = new RevSliderFolder();
				$is = $folder->init_folder_by_id($id);

				if ($is === true) {
					$folder->delete_slider();
					$this->ajax_response_success($this->l('Folder Deleted'));
				} else {
					$this->ajax_response_error($this->l('Folder Deletion Failed'));
				}

				break;
			case 'update_slider_tags':
				$id = $this->get_val($data, 'id');
				$tags = $this->get_val($data, 'tags');

				$return = $slider->update_slider_tags($id, $tags);

				if ($return == true) {
					$this->ajax_response_success($this->l('Tags Updated'));
				} else {
					$this->ajax_response_error($this->l('Failed to Update Tags'));
				}

				break;
			case 'save_slider_folder':
				$folder = new RevSliderFolder();
				$children = $this->get_val($data, 'children');
				$folder_id = $this->get_val($data, 'id');

				$return = $folder->add_slider_to_folder($children, $folder_id);

				if ($return == true) {
					$this->ajax_response_success($this->l('Slider Moved to Folder'));
				} else {
					$this->ajax_response_error($this->l('Failed to Move Slider Into Folder'));
				}

				break;
			case 'update_slider_name':
			case 'update_folder_name':
				$slider_id = $this->get_val($data, 'id');
				$new_title = $this->get_val($data, 'title');
                $slider = new RevSliderSlider($slider_id);
                $slider->title = $title;
                $return = $slider->update();

				if ($return != false) {
					$this->ajax_response_data(['title' => $return], $this->l('Title updated'));
				} else {
					$this->ajax_response_error($this->l('Failed to update Title'));
				}

				break;
			case 'preview_slider':
				$slider_id = $this->get_val($data, 'id');
				$slider_data = $this->get_val($data, 'data');
				$title = $this->l('Slider Revolution Preview');

				if ((int) ($slider_id) > 0 && empty($slider_data)) {
					$slider = new RevSliderSlider($slider_id);

					$output = new RevSliderOutput();
					ob_start();
					$slider = $output->add_slider_to_stage($slider_id);
					$content = ob_get_contents();
					ob_clean();
					ob_end_clean();
					// track
					// $content = '[rev_slider alias="' .  RevLoader::esc_attr($slider->get_alias()) . '"]';
				} else

				if (!empty($slider_data)) {
					$_slides = [];
					$_static = [];
					$slides = [];
					$static_slide = [];

					$_slider = [
						'id'       => $slider_id,
						'title'    => 'Preview',
						'alias'    => 'preview',
						'settings' => json_encode(['version' => RS_REVISION]),
						'params'   => $this->get_val($slider_data, 'slider'),
					];
					$slide_order = json_decode(stripslashes($this->get_val($slider_data, ['slide_order'])), true);

					foreach ($slider_data as $sk => $sd) {

						if (in_array($sk, ['slider', 'slide_order'], true)) {
							continue;
						}

						if (strpos($sk, 'static_') !== false) {
							$_static = [
								'params' => stripslashes($this->get_val($sd, 'params')),
								'layers' => stripslashes($this->get_val($sd, 'layers')),
							];
						} else {
							$_slides[$sk] = [
								'id'          => $sk,
								'slider_id'   => $slider_id,
								'slide_order' => array_search($sk, $slide_order),
								'params'      => stripslashes($this->get_val($sd, 'params')),
								'layers'      => $this->get_val($sd, 'layers'),
								'settings'    => ['version' => RS_REVISION],
							];
						}

					}

					$output = new RevSliderOutput();
					$slider->init_by_data($_slider);

					if ($slider->is_stream() || $slider->is_posts()) {
						$slides = $slider->get_slides_for_output();
					} else {

						if (!empty($_slides)) {
							// reorder slides
							usort($_slides, [$this, 'sort_by_slide_order']);

							foreach ($_slides as $_slide) {
								$slide = new RevSliderSlide();
								$slide->init_by_data($_slide);

								if ($slide->get_param(['publish', 'state'], 'published') === 'unpublished') {
									continue;
								}

								$slides[] = $slide;
							}

						}

					}

					if (!empty($_static)) {
						$slide = new RevSliderSlide();
						$slide->init_by_data($_static);
						$static_slide = $slide;
					}

					$output->set_slider($slider);
					$output->set_current_slides($slides);
					$output->set_static_slide($static_slide);
					$output->set_preview_mode(true);

					ob_start();
					$slider = $output->add_slider_to_stage($slider_id);
					$content = ob_get_contents();
					ob_clean();
					ob_end_clean();
				}

				// get dimensions of slider
				$size = [
					'width'  => $slider->get_param(['size', 'width'], []),
					'height' => $slider->get_param(['size', 'height'], []),
					'custom' => $slider->get_param(['size', 'custom'], []),
				];

				if (empty($size['width'])) {
					$size['width'] = [
						'd' => $this->get_val($this->global_settings, ['size', 'desktop'], '1240'),
						'n' => $this->get_val($this->global_settings, ['size', 'notebook'], '1024'),
						't' => $this->get_val($this->global_settings, ['size', 'tablet'], '778'),
						'm' => $this->get_val($this->global_settings, ['size', 'mobile'], '480'),
					];
				}

				if (empty($size['height'])) {
					$size['height'] = [
						'd' => '868',
						'n' => '768',
						't' => '960',
						'm' => '720',
					];
				}

				global $revslider_is_preview_mode;
				$revslider_is_preview_mode = true;

				$rev_slider_front = new RevSliderFront();

				// $post = $this->create_fake_post($content, $title);

				ob_start();
				include RS_PLUGIN_PATH . 'public/views/revslider-page-template.php';
				$html = ob_get_contents();
				ob_clean();
				ob_end_clean();

				$this->ajax_response_data(
					[
						'html'       => $html,
						'size'       => $size,
						'layouttype' => $slider->get_param(
							'layouttype',
							'fullwidth'
						),
					]
				);
				exit;
				break;

			case 'check_system':
				// recheck the connection to themepunch server
				$update = new RevSliderUpdate(RS_REVISION);
				$update->force = true;
				$update->_retrieve_version_info();

				$fun = new RevSliderFunctionsAdmin();
				$system = $fun->get_system_requirements();

				$this->ajax_response_data(['system' => $system]);
				break;
			case 'load_plugin':
				$plugin = $this->get_val($data, 'plugin', ['all']);
				$plugin_uid = $this->get_val($data, 'plugin_uid', false);
				$plugin_slider_id = $this->get_val($data, 'plugin_id', false);
				$refresh_from_server = $this->get_val($data, 'refresh_from_server', false);
				$get_static_slide = $this->_truefalse($this->get_val($data, 'static', false));

				if ($plugin_uid === false) {
					$plugin_uid = $plugin_slider_id;
				}

				$admin = new RevSliderFunctionsAdmin();
				$plugins = $admin->get_full_library($plugin, $plugin_uid, $refresh_from_server, $get_static_slide);

				$this->ajax_response_data(['plugins' => $plugins]);
				break;
			case 'set_favorite':
				$do = $this->get_val($data, 'do', 'add');
				$type = $this->get_val($data, 'type', 'slider');
				$id = RevLoader::esc_attr($this->get_val($data, 'id'));

				$favorite = new RevSliderFavorite();
				$favorite->set_favorite($do, $type, $id);

				$this->ajax_response_success($this->l('Favorite Changed'));
				break;
			case 'load_library_object':
				$library = new RevSliderObjectLibrary();

				$cover = false;
				$id = $this->get_val($data, 'id');
				$type = $this->get_val($data, 'type');

				if ($type == 'thumb') {
					$thumb = $library->_get_object_thumb($id, 'thumb');
				} else

				if ($type == 'video') {
					$thumb = $library->_get_object_thumb($id, 'video_full', true);
					$cover = $library->_get_object_thumb($id, 'cover', true);
				} else

				if ($type == 'layers') {
					$thumb = $library->_get_object_layers($id);
				} else {
					$thumb = $library->_get_object_thumb($id, 'orig', true);

					if (isset($thumb['error']) && $thumb['error'] === false) {
						$orig = $this->get_val($thumb, 'url', false);
						$url = $library->get_correct_size_url($id, $type);

						if ($url !== '') {
							$thumb['url'] = $url;
						}

					}

				}

				if (isset($thumb['error']) && $thumb['error'] !== false) {
					$this->ajax_response_error($this->l('Object could not be loaded'));
				} else {

					if ($type == 'layers') {
						$return = ['layers' => $this->get_val($thumb, 'data')];
					} else {
						$return = ['url' => $this->get_val($thumb, 'url')];
					}

					if ($cover !== false) {

						if (isset($cover['error']) && $cover['error'] !== false) {
							$this->ajax_response_error($this->l('Video cover could not be loaded'));
						}

						$return['cover'] = $this->get_val($cover, 'url');
					}

					$this->ajax_response_data($return);
				}

				break;
			case 'create_slide':
				$slider_id = $this->get_val($data, 'slider_id', false);
				$amount = $this->get_val($data, 'amount', 1);
				$amount = (int) ($amount);
				$slide_ids = [];

				if ((int) ($slider_id) > 0 && ($amount > 0 && $amount < 50)) {

					for ($i = 0; $i < $amount; $i++) {
						$slide_ids[] = $slide->create_slide($slider_id);
					}

				}

				if (!empty($slide_ids)) {
					$this->ajax_response_data(['slide_id' => $slide_ids]);
				} else {
					$this->ajax_response_error($this->l('Could not create Slide'));
				}

				break;
			case 'create_slider':
				/**
				 * 1. create a blank Slider
				 * 2. create a blank Slide
				 * 3. create a blank Static Slide
				 */

				$slide_id = false;
				$slider_id = $slider->create_blank_slider();

				if ($slider_id !== false) {
					$slide_id = $slide->create_slide($slider_id); // normal slide
					$slide->create_slide($slider_id, '', true); // static slide
				}

				if ($slide_id !== false) {
					$this->ajax_response_data(
						[
							'slide_id'  => $slide_id,
							'slider_id' => $slider_id,
						]
					);
				} else {
					$this->ajax_response_error($this->l('Could not create Slider'));
				}

				break;

			case 'get_layers_by_slide':
				$slide_id = $this->get_val($data, 'slide_id');
                $slide = new RevSliderSlide($slide_id);
				$layers = $slide->get_layers();

				$this->ajax_response_data(['layers' => $layers]);
				break;
			case 'activate_addon':
				$handle = $this->get_val($data, 'addon');
				$update = $this->get_val($data, 'update', false);
				$addon = new RevSliderAddons();

				$return = $addon->install_addon($handle, $update);

				if ($return === true) {
					// return needed files of the plugin somehow
					$data = [];
					$data = RevLoader::apply_filters('revslider_activate_addon', $data, $handle);

					$this->ajax_response_data([$handle => $data]);
				} else {
					$error = ($return === false) ? $this->l('AddOn could not be activated') : $return;

					$this->ajax_response_error($error);
				}

				break;
			case 'deactivate_addon':
				$handle = $this->get_val($data, 'addon');
				$addon = new RevSliderAddons();
				$return = $addon->deactivate_addon($handle);

				if ($return) {
					// return needed files of the plugin somehow
					$this->ajax_response_success($this->l('AddOn deactivated'));
				} else {
					$this->ajax_response_error($this->l('AddOn could not be deactivated'));
				}

				break;
			case 'create_draft_page':
				$this->ajax_response_data(['create_draft_page' => 'No need to draft page']);

				break;
			case 'generate_attachment_metadata':
				$this->generate_attachment_metadata();
				$this->ajax_response_success('');
				break;
			case 'export_layer_group': // developer function only :)
				$title = $this->get_val($data, 'title', $this->get_request_var('title'));
				$videoid = (int) ($this->get_val($data, 'videoid', $this->get_request_var('videoid')));
				$thumbid = (int) ($this->get_val($data, 'thumbid', $this->get_request_var('thumbid')));
				$layers = $this->get_val($data, 'layers', $this->get_request_var('layers'));

				$export = new RevSliderSliderExport($title);
				$url = $export->export_layer_group($videoid, $thumbid, $layers);

				$this->ajax_response_data(['url' => $url]);
				break;

			case 'load_wordpress_image':
				$id = $this->get_val($data, 'id', 0);
				$type = $this->get_val($data, 'type', 'orig');

				// $img = wp_get_attachment_image_url($id, $type);
				$img = '';

				if (empty($img)) {
					$this->ajax_response_error($this->l('Image could not be loaded'));
				}

				$this->ajax_response_data(['url' => $img]);
				break;
			case 'load_library_image':
				$images = (!is_array($data)) ? (array) $data : $data;
				$images = RevSliderFunction::esc_attr_deep($images);
				$images = self::esc_js_deep($images);
				$img_data = [];

				if (!empty($images)) {
					$templates = new RevSliderTemplate();
					$obj = new RevSliderObjectLibrary();

					foreach ($images as $image) {
						$type = $this->get_val($image, 'librarytype');
						$img = $this->get_val($image, 'id');
						$ind = $this->get_val($image, 'ind');
						$mt = $this->get_val($image, 'mediatype');

						switch ($type) {
						case 'plugintemplates':
						case 'plugintemplateslides':
							$img = $templates->_check_file_path($img, true);
							$img_data[] = [
								'ind'       => $ind,
								'url'       => $img,
								'mediatype' => $mt,
							];
							break;
						case 'image':
						case 'images':
						case 'layers':
						case 'objects':
							$get = ($mt === 'video') ? 'video_thumb' : 'thumb';
							$img = $obj->_get_object_thumb($img, $get, true);

							if ($this->get_val($img, 'error', false) === false) {
								$img_data[] = [
									'ind'       => $ind,
									'url'       => $this->get_val($img, 'url'),
									'mediatype' => $mt,
								];
							}

							break;
						case 'videos':
							$get = ($mt === 'img') ? 'video' : 'video_thumb';
							$img = $obj->_get_object_thumb($img, $get, true);

							if ($this->get_val($img, 'error', false) === false) {
								$img_data[] = [
									'ind'       => $ind,
									'url'       => $this->get_val($img, 'url'),
									'mediatype' => $mt,
								];
							}

							break;
						}

					}

				}

				$this->ajax_response_data(['data' => $img_data]);
				break;
			case 'get_help_directory':

				if (class_exists('RevSliderHelp')) {
					$helper = new RevSliderHelp();
					$help_data = $helper->getIndex();
					$this->ajax_response_data(['data' => $help_data]);
				} else {
					$return = '';
				}

				break;
			case 'get_tooltips':

				if (class_exists('RevSliderTooltips')) {
					$helper = new RevSliderTooltips();
					$tooltips = $helper->getTooltips();
					$this->ajax_response_data(['data' => $tooltips]);
				} else {
					$return = '';
				}

				break;
			case 'set_tooltip_preference':
				RevLoader::update_option('revslider_hide_tooltips', true);
				$return = 'Preference Updated';
				break;
			case 'save_color_preset':
				$presets = $this->get_val($data, 'presets', []);
				$color_presets = RSColorpicker::save_color_presets($presets);
				$this->ajax_response_data(['presets' => $color_presets]);
				break;
			case 'get_facebook_photosets':

				if (!empty($data['url'])) {
					$facebook = new RevSliderFacebook();
					$return = $facebook->get_photo_set_photos_options($data['url'], $data['album'], $data['app_id']);

					if (empty($return)) {
						$error = $this->l('Could not fetch Facebook albums');
						$this->ajax_response_error($error);
					} else {

						if (!isset($return[0]) || $return[0] != 'error') {
							$this->ajax_response_success($this->l('Successfully fetched Facebook albums'), ['html' => implode(' ', $return)]);
						} else {
							$error = $return[1];
							$this->ajax_response_error($error);
						}

					}

				} else {
					$this->ajax_response_success($this->l('Cleared Albums'), ['html' => implode(' ', $return)]);
				}

				break;
			case 'get_flickr_photosets':
				$error = $this->l('Could not fetch flickr photosets');

				if (!empty($data['url']) && !empty($data['key'])) {
					$flickr = new RevSliderFlickr($data['key']);
					$user_id = $flickr->get_user_from_url($data['url']);
					$return = $flickr->get_photo_sets($user_id, $data['set'], $data['count']);

					if (!empty($return)) {
						$this->ajax_response_success($this->l('Successfully fetched flickr photosets'), ['data' => ['html' => implode(' ', $return)]]);
					} else {
						$error = $this->l('Could not fetch flickr photosets');
					}

				} else {

					if (empty($data['url']) && empty($data['key'])) {
						$this->ajax_response_success($this->l('Cleared Photosets'), ['html' => implode(' ', $return)]);
					} else

					if (empty($data['url'])) {
						$error = $this->l('No User URL - Could not fetch flickr photosets');
					} else {
						$error = $this->l('No API KEY - Could not fetch flickr photosets');
					}

				}

				$this->ajax_response_error($error);
				break;
			case 'get_youtube_playlists':

				if (!empty($data['id'])) {
					$youtube = new RevSliderYoutube(trim($data['api']), trim($data['id']));
					$return = $youtube->get_playlist_options($data['playlist']);
					$this->ajax_response_success($this->l('Successfully fetched YouTube playlists'), ['data' => ['html' => implode(' ', $return)]]);
				} else {
					$this->ajax_response_error($this->l('Could not fetch YouTube playlists'));
				}

				break;
			case 'fix_database_issues':
				RevLoader::update_option('revslider_table_version', '1.0.0');

				RevSliderFront::create_tables(true);

				$this->ajax_response_success($this->l('Slider Revolution database structure was updated'));
				break;
			case 'trigger_font_deletion':
				$this->delete_google_fonts();

				$this->ajax_response_success($this->l('Downloaded Google Fonts will be updated'));
				break;
			case 'get_same_aspect_ratio':
				$images = $this->get_val($data, 'images', []);
				$return = $this->get_same_aspect_ratio_images($images);

				$this->ajax_response_data(['images' => $return]);
				break;
			case 'get_addons_sizes':
				$addons = $this->get_val($data, 'addons', []);
				$sizes = $this->get_addon_sizes($addons);

				$this->ajax_response_data(['addons' => $sizes]);
				break;
			case 'get_v5_slider_list':
				$admin = new RevSliderFunctionsAdmin();
				$sliders = $admin->get_v5_slider_data();

				$this->ajax_response_data(['slider' => $sliders]);
				break;
			case 'reimport_v5_slider':
				$status = false;

				if (!empty($data['id'])) {
					$admin = new RevSliderFunctionsAdmin();
					$status = $admin->reimport_v5_slider($data['id']);
				}

				if ($status === false) {
					$this->ajax_response_error($this->l('Slider could not be transfered to v6'));
				} else {
					$this->ajax_response_success($this->l('Slider transfered to v6'));
				}

				break;
			default:
				$return = ''; // ''is not allowed to be added directly in apply_filters(), so its needed like this
				$return = RevLoader::apply_filters('revslider_do_ajax', $return, $action, $data);

				// track custom work for addons

				if ('wp_ajax_save_values_revslider-weather-addon' == $action) {
					RevLoader::update_option('revslider_weather_addon', $data['revslider_weather_form']);
					$return = 'Saved';
				} else

				if ('wp_ajax_get_values_revslider-weather-addon' == $action) {
					$return = RevLoader::values_weather();
				} else

				if ('wp_ajax_enable_revslider-maintenance-addon' == $action) {
					RevLoader::change_addon_status_overwrite(1);
					$return = $this->l('maintenance AddOn enabled', 'revslider-maintenance-addon');
				} else

				if ('wp_ajax_disable_revslider-maintenance-addon' == $action) {
					RevLoader::change_addon_status_overwrite(0);
					$return = $this->l('maintenance AddOn disabled', 'revslider-maintenance-addon');
				} else

				if ('wp_ajax_get_values_revslider-maintenance-addon' == $action) {
					$return = RevLoader::values_maintenance_overwrite();
				} else

				if ('wp_ajax_save_values_revslider-maintenance-addon' == $action) {
					$return = RevLoader::save_maintenance_overwrite();

					if (empty($return) || !$return) {
						$return = $this->l('Configuration could not be saved', 'revslider-maintenance-addon');
					} else {
						$return = $this->l('Maintenance Configuration saved', 'revslider-maintenance-addon');
					}

				} else

				if ('wp_ajax_enable_revslider-backup-addon' == $action) {
					RevLoader::change_backup_addon_status(1);
					$return = $this->l('Backups AddOn enabled', 'revslider-backup-addon');
				} else

				if ('wp_ajax_disable_revslider-backup-addon' == $action) {
					RevLoader::change_backup_addon_status(1);
					$return = $this->l('Backups AddOn disabled', 'revslider-backup-addon');
				} else

				if ('fetch_slide_backups' == $action) {
					$slide_data = RevLoader::fetch_slide_backups_overwrite($data['slideID'], true);
					$return = ['data' => $slide_data];
				} else

				if ('restore_slide_backup' == $action) {
					$backup_id = (int) ($data['id']);
					$slide_id = $data['slide_id'];
					$session_id = RevLoader::esc_attr($data['session_id']);
					$response = RevLoader::restore_slide_backup($backup_id, $slide_id, $session_id);

					if ($response !== true) {
						$f = new RevSliderFunction();
						$f->throw_error($this->l('Backup restoration failed...', 'rs_backup'));
					}

					$return = $this->l('Backup restored, redirecting...', 'rs_backup');
				} else

				if ('wp_ajax_save_values_revslider-domain-switch-addon' == $action) {

					$revslider_domain_switch = [];

					if (isset($data['revslider_domain_switch_form'])) {
						parse_str($data['revslider_domain_switch_form'], $revslider_domain_switch);

						if (!isset($revslider_domain_switch['revslider-domain-switch-addon-old']) || empty($revslider_domain_switch['revslider-domain-switch-addon-old'])) {
							return Revloader::__('Old domain can not be empty');
						}

						if (!isset($revslider_domain_switch['revslider-domain-switch-addon-new']) || empty($revslider_domain_switch['revslider-domain-switch-addon-new'])) {
							return Revloader::__('New domain can not be empty');
						}

						$rso = str_replace('/', '\/', $revslider_domain_switch['revslider-domain-switch-addon-old']);
						$rsn = str_replace('/', '\/', $revslider_domain_switch['revslider-domain-switch-addon-new']);

						//go through all tables and replace image URLs with new names
						global $wpdb;

						$sql = Db::getInstance()->prepare("UPDATE " . _DB_PREFIX_ . RevSliderFront::TABLE_SLIDER . " SET `params` = replace(`params`, %s, %s)", [$rso, $rsn]);
						Db::getInstance()->execute($sql);
						$sql = Db::getInstance()->prepare("UPDATE " . _DB_PREFIX_ . RevSliderFront::TABLE_SLIDES . " SET `params` = replace(`params`, %s, %s)", [$rso, $rsn]);
						Db::getInstance()->execute($sql);
						$sql = Db::getInstance()->prepare("UPDATE " . _DB_PREFIX_ . RevSliderFront::TABLE_SLIDES . " SET `layers` = replace(`layers`, %s, %s)", [$rso, $rsn]);
						Db::getInstance()->execute($sql);
						$sql = Db::getInstance()->prepare("UPDATE " . _DB_PREFIX_ . RevSliderFront::TABLE_STATIC_SLIDES . " SET `params` = replace(`params`, %s, %s)", [$rso, $rsn]);
						Db::getInstance()->execute($sql);
						$sql = Db::getInstance()->prepare("UPDATE " . _DB_PREFIX_ . RevSliderFront::TABLE_STATIC_SLIDES . " SET `layers` = replace(`layers`, %s, %s)", [$rso, $rsn]);
						Db::getInstance()->execute($sql);
						$return = RevLoader::__("Domains successfully changed in all sliders", 'domain-switch');

					} else {
						$return = RevLoader::__("No Data Send", 'domain-switch');
					}

				}

				if ($return) {

					if (is_array($return)) {
						// if(isset($return['message'])) $this->ajax_response_success($return["message"]);

						if (isset($return['message'])) {
							$this->ajax_response_data(
								[
									'message' => $return['message'],
									'data'    => $return['data'],
								]
							);
						}

						$this->ajax_response_data(['data' => $return['data']]);
					} else {
						$this->ajax_response_success($return);
					}

				} else {
					$return = '';
				}

				break;
			}

		} catch (Exception $e) {
			$message = $e->getMessage();

			if (in_array($action, ['preview_slide', 'preview_slider'])) {
				echo $message;
				RevLoader::wp_die();
			}

			$this->ajax_response_error($message);
		}

		// it's an ajax action, so exit
		$this->ajax_response_error($this->l('No response on action'));
		RevLoader::wp_die();
	}

	/**
	 * Ajax handling for frontend, no privileges here
	 */
	public function do_front_ajax_action() {

		$token = $this->get_post_var('token', false);

		// verify the token
		$is_verified = wp_verify_nonce($token, 'RevSlider_Front');

		$error = false;

		if ($is_verified) {
			$data = $this->get_post_var('data', false);

			switch ($this->get_post_var('client_action', false)) {
			case 'get_slider_html':
				$alias = $this->get_post_var('alias', '');
				$usage = $this->get_post_var('usage', '');
				$modal = $this->get_post_var('modal', '');
				$layout = $this->get_post_var('layout', '');
				$offset = $this->get_post_var('offset', '');
				$id = (int) ($this->get_post_var('id', 0));

				// check if $alias exists in database, transform it to id

				if ($alias !== '') {
					$sr = new RevSliderSlider();
					$id = (int) ($sr->alias_exists($alias, true));
				}

				if ($id > 0) {
					$html = '';
					ob_start();
					$slider = new RevSliderOutput();
					$slider->set_ajax_loaded();

					$slider_class = $slider->add_slider_to_stage($id, $usage, $layout, $offset, $modal);
					$html = ob_get_contents();
					ob_clean();
					ob_end_clean();


					$result = (!empty($slider_class) && $html !== '') ? true : false;

					if (!$result) {
						$error = $this->l('Slider not found');
					} else {

						if ($html !== false) {
							$this->ajax_response_data($html);
						} else {
							$error = $this->l('Slider not found');
						}

					}

				} else {
					$error = $this->l('No Data Received');
				}

				break;
			}

		} else {
			$error = true;
		}

		if ($error !== false) {
			$show_error = ($error !== true) ? $this->l('Loading Error') : $this->l('Loading Error: ') . $error;

			$this->ajax_response_error($show_error, false);
		}

		exit;
	}

	/**
	 * echo json ajax response as error
	 *
	 * @before: RevSliderBaseAdmin::ajaxResponseError();
	 */
	protected function ajax_response_error($message, $data = null) {

		$this->ajax_response(false, $message, $data, true);
	}

	/**
	 * echo ajax success response with redirect instructions
	 *
	 * @before: RevSliderBaseAdmin::ajaxResponseSuccessRedirect();
	 */
	protected function ajax_response_redirect($message, $url) {

		$data = [
			'is_redirect'  => true,
			'redirect_url' => $url,
		];

		$this->ajax_response(true, $message, $data, true);
	}

	/**
	 * echo json ajax response, without message, only data
	 *
	 * @before: RevSliderBaseAdmin::ajaxResponseData()
	 */
	protected function ajax_response_data($data) {

		$data = (gettype($data) == 'string') ? ['data' => $data] : $data;

		$this->ajax_response(true, '', $data);
	}

	/**
	 * echo ajax success response
	 *
	 * @before: RevSliderBaseAdmin::ajaxResponseSuccess();
	 */
	protected function ajax_response_success($message, $data = null) {

		$this->ajax_response(true, $message, $data, true);
	}

	/**
	 * echo json ajax response
	 * before: RevSliderBaseAdmin::ajaxResponse
	 */
	private function ajax_response($success, $message, $data = null) {

		$response = [
			'success' => $success,
			'message' => $message,
		];

		if (!empty($data)) {

			if (gettype($data) == 'string') {
				$data = ['data' => $data];
			}

			$response = array_merge($response, $data);
		}

		echo json_encode($response);

		RevLoader::wp_die();
	}

	/**
	 * set the page that should be shown
	 **/
	private function set_current_page() {

		$view = $this->get_get_var('view');
		$this->view = (empty($view)) ? 'sliders' : $this->get_get_var('view');
	}

	/**
	 * include/display the previously set page
	 * only allow certain pages to be showed
	 **/
	public function display_admin_page() {

		try {

			if (!in_array($this->view, $this->allowed_views)) {
				$this->throw_error($this->l('Bad Request'));
			}

			switch ($this->view) {
			// switch URLs to corresponding php files
			case 'slide':
				$view = 'builder';
				break;
			case 'sliders':
			default:
				$view = 'overview';
				break;
			}

			$this->validate_filepath($this->path_views . $view . '.php', 'View');

			include $this->path_views . 'header.php';
			include $this->path_views . $view . '.php';
			include $this->path_views . 'footer.php';

		} catch (Exception $e) {
			$this->show_error($this->view, $e->getMessage());
		}

	}

	/**
	 * show an nice designed error
	 **/
	public function show_error($view, $message) {

		echo '<div class="rs-error">';
		echo $this->l('Slider Revolution encountered the following error: ');
		echo RevLoader::esc_attr($view);
		echo ' - Error: <span>';
		echo RevLoader::esc_attr($message);
		echo '</span>';
		echo '</div>';
		exit;
	}

	/**
	 * validate that some file exists, if not - throw error
	 *
	 * @before: RevSliderFunction::validateFilepath
	 */
	public function validate_filepath($filepath, $prefix = null) {

		if (file_exists($filepath) == true) {
			return true;
		}

		$prefix = ($prefix == null) ? 'File' : $prefix;
		$message = $prefix . ' ' . RevLoader::esc_attr($filepath) . ' not exists!';

		$this->throw_error($message);
	}

	/**
	 * esc attr recursive
	 *
	 * @since: 6.0
	 */
	public static function esc_js_deep($value) {

		$value = is_array($value) ? array_map(['RevSliderAdmin', 'esc_js_deep'], $value) : RevLoader::esc_js($value);

		return $value;
	}

	/**
	 * generate missing attachement metadata for images
	 *
	 * @since: 6.0
	 **/
	public function generate_attachment_metadata() {

		$rs_meta_create = RevLoader::get_option('rs_image_meta_todo', []);

		if (!empty($rs_meta_create)) {

			foreach ($rs_meta_create as $attach_id => $save_dir) {

				if ($attach_data = @wp_generate_attachment_metadata($attach_id, $save_dir)) {
					@wp_update_attachment_metadata($attach_id, $attach_data);
				}

				unset($rs_meta_create[$attach_id]);
				RevLoader::update_option('rs_image_meta_todo', $rs_meta_create);
			}

		}

	}

}
