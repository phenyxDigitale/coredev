<?php

class RevSliderTemplate extends RevSliderFunction {

	private $templates_list = 'revslider/get-list.php';
	private $templates_download = 'revslider/download.php';

	private $templates_server_path = '/revslider/images/';
	private $templates_path = '/revslider/templates/';

	private $curl_check = null;

	const PHENYX_VERSION = '2.0';

	/**
	 * Download template by UID (also validates if download is legal)
	 * @since: 5.0.5
	 */

	public function _download_template($uid) {

		$rslb = new RevSliderLoadBalancer();
		$return = false;
		$uid = $this->clear_uid($uid);
		$uid = RevLoader::esc_attr($uid);
		$code = (RevLoader::get_option('revslider-valid', 'false') == false) ? '' : RevLoader::get_option('revslider-code', '');

		$rs_templates = RevLoader::get_option('rs-templates', '');

		$template = '';

		foreach ($rs_templates['slider'] as $obj) {

			if ($uid == $obj['uid']) {
				$template = $obj['zip'];
				break;
			}

		}

		$upload_dir = RevLoader::wp_upload_dir();

		if (RevLoader::wp_mkdir_p($upload_dir['basedir'] . $this->templates_path)) {
			//check here to not flood the server
			$data = [
				'code'         => urlencode($code),
				'shop_version' => urlencode(self::PHENYX_VERSION),
				'version'      => urlencode(RS_REVISION),
				'uid'          => urlencode($uid),
				'product'      => urlencode(RS_PLUGIN_SLUG),
			];

			$url = 'http://revapi.smartdatasoft.net/v6/call/index.php?code=' . $code . '&template=' . $template;
			$request = RevLoader::wp_remote_post($url, [
				'user-agent' => 'php/; ' . RevLoader::get_bloginfo('url'),
				'body'       => '',
				'timeout'    => 400,
			]);

			if ($request && $request["info"]["http_code"] == "200") {

				if ($response = $this->get_val($request, 'body')) {

					if ($response !== 'invalid') {
						//add stream as a zip file
						$file = $upload_dir['basedir'] . $this->templates_path . $uid . '.zip';
						@mkdir(dirname($file));
						//@mkdir(dirname($file), 777, true);
						$ret = @file_put_contents($file, base64_decode($response));

						if ($ret !== false) {
							//return $file so it can be processed. We have now downloaded it into a zip file
							$return = $file;
						} else {
							$return = ['error' => $this->l('Can\'t write the file into the uploads folder of WordPress, please change permissions and try again!')];
						}

					} else {
						$error = ($this->get_addition('selling') === true) ? $this->l('License Key is invalid') : $this->l('Purchase Code is invalid');

						$return = ['error' => $error];
					}

				}

			} else {
				$return = ['error' => $this->l('Can\'t connect programatically to the ThemePunch servers, please check your webserver settings')];
			}

		} else {
			$return = ['error' => $this->l('Can\'t write into the uploads folder of WordPress, please change permissions and try again!')];
		}

		return $return;
	}

	/**
	 * Delete the Template file
	 * @since: 5.0.5
	 */
	public function _delete_template($uid) {

		$uid = $this->clear_uid($uid);
		$uid = RevLoader::esc_attr($uid);
		$upload_dir = RevLoader::wp_upload_dir();

		if (RevLoader::wp_mkdir_p($upload_dir['basedir'] . $this->templates_path)) {
			$file = $upload_dir['basedir'] . $this->templates_path . '/' . $uid . '.zip';

			if (file_exists($file)) {
				//delete file
				return unlink($file);
			}

		}

		return false;
	}

	/**
	 * Get the Templatelist from servers
	 * @since: 5.0.5
	 */
	public function _get_template_list($force = false) {

		$rslb = new RevSliderLoadBalancer();
		$last_check = RevLoader::get_option('revslider-templates-check');

		if ($last_check == false) {

			$last_check = 172801;
			RevLoader::update_option('revslider-templates-check', time());
		}

		// Get latest Templates

		if (time() - $last_check > 345600 || $force == true) {

			RevLoader::update_option('revslider-templates-check', time());

			$hash = RevLoader::get_option('revslider-templates-hash', '');
			$code = (RevLoader::get_option('revslider-valid', 'false') == false) ? '' : RevLoader::get_option('revslider-code', '');
			$data = [
				'code'         => urlencode($code),
				'shop_version' => urlencode(self::PHENYX_VERSION),
				'hash'         => '9b55a617181b6b6122302b781de536c0',
				'version'      => urlencode(RS_REVISION),
				'product'      => urlencode(RS_PLUGIN_SLUG),
			];

			RevLoader::update_template_json('rs-templates-new');

			$this->update_template_list();
		}

	}

	/**
	 * Update the Templatelist, move rs-templates-new into rs-templates
	 * @since: 5.0.5
	 */
	private function update_template_list() {

		$new = RevLoader::get_option('rs-templates-new', false);
		$cur = RevLoader::get_option('rs-templates', []);
		$counter = 0;

		if ($new !== false && !empty($new) && is_array($new)) {

			if (empty($cur)) {
				$cur = $new;
				$counter = (isset($cur['slider']) && is_array($cur['slider'])) ? count($cur['slider']) : $counter;
			} else {

				if (isset($new['slider']) && is_array($new['slider'])) {

					if (isset($cur['slider']) && is_array($cur['slider']) && isset($new['slider']) && is_array($cur['slider'])) {
						$_n = count($new['slider']);
						$_c = count($cur['slider']);
						$counter = ($_n > $_c) ? $_n - $_c : $counter;
					}

					foreach ($new['slider'] as $n) {
						$found = false;

						if (isset($cur['slider']) && is_array($cur['slider'])) {

							foreach ($cur['slider'] as $ck => $c) {

								if ($c['uid'] == $n['uid']) {

									if (version_compare($c['version'], $n['version'], '<')) {
										$n['is_new'] = true;
										$n['push_image'] = true; //push to get new image and replace
									}

									if (isset($c['is_new'])) {
										$n['is_new'] = true;
									}

									//is_new will stay until update is done

									$n['exists'] = true; //if this flag is not set here, the template will be removed from the list

									if (isset($n['new_slider'])) {
										unset($n['new_slider']); //remove this again, as the new flag should be removed now
									}

									$cur['slider'][$ck] = $n;
									$found = true;

									break;
								}

							}

						}

						if (!$found) {
							$n['exists'] = true;
							$n['new_slider'] = true;
							$cur['slider'][] = $n;
						}

					}

					foreach ($cur['slider'] as $ck => $c) {
						//remove no longer available Slider

						if (!isset($c['exists'])) {
							unset($cur['slider'][$ck]);
						} else {
							unset($cur['slider'][$ck]['exists']);
						}

					}

					$cur['slides'] = $new['slides']; // push always all slides
				}

			}

			RevLoader::update_option('rs-templates', $cur, false);
			RevLoader::update_option('rs-templates-new', false, false);

			//$this->_update_images();
		}

		RevLoader::update_option('rs-templates-counter', $counter, false);
	}

	/**
	 * Remove the is_new attribute which shows the "update available" button
	 * @since: 5.0.5
	 */
	public function remove_is_new($uid) {

		$cur = RevLoader::get_option('rs-templates', []);

		if (isset($cur['slider']) && is_array($cur['slider'])) {

			foreach ($cur['slider'] as $ck => $c) {

				if ($c['uid'] == $uid) {
					unset($cur['slider'][$ck]['is_new']);
					break;
				}

			}

		}

		RevLoader::update_option('rs-templates', $cur, false);
	}

	/**
	 * Update the Images get them from Server and check for existance on each image
	 * @since: 5.0.5
	 */
	private function _update_images($img = false) {

		$rslb = new RevSliderLoadBalancer();
		$templates = RevLoader::get_option('rs-templates', []);
		//track
		//$chk	= $this->check_curl_connection();
		//$curl	= ($chk) ? new WP_Http_Curl() : false;
		$curl = true;
		$url = $rslb->get_url('templates', 0, true);
		$reload = [];

		$loaded = false;

		if (!empty($templates) && is_array($templates)) {
			$upload_dir = RevLoader::wp_upload_dir(); // Set upload folder

			if (!empty($templates['slider']) && is_array($templates['slider'])) {

				foreach ($templates['slider'] as $key => $temp) {

					if ($img !== false) {
						//we want to download a certain image, check for it

						if ($this->get_val($temp, 'img') !== $img) {
							continue;
						}

					}

					// Check folder permission and define file location

					if (RevLoader::wp_mkdir_p($upload_dir['basedir'] . $this->templates_path)) {
						$file = $upload_dir['basedir'] . $this->templates_path . '/' . $temp['img'];

						if (!file_exists($file) || isset($temp['push_image'])) {

							if ($curl !== false) {
								$done = false;
								$count = 0;

								do {
									//track
									//$image_data = @$curl->request($url.'/'.$this->templates_server_path.$temp['img']); // Get image data
									$image_data = RevLoader::wp_remote_post($url . '/' . $this->templates_server_path . $temp['img'], [
										'user-agent' => 'revphp; ' . RevLoader::get_bloginfo('url'),
										'body'       => [
											'item'    => urlencode(RS_PLUGIN_SLUG),
											'version' => urlencode(RS_REVISION),
										],
										'timeout'    => 45,
									]);

									if (!RevLoader::is_wp_error($image_data) && isset($image_data['body']) && isset($image_data['response']) && isset($image_data['response']['code']) && $image_data['response']['code'] == '200') {
										$image_data = $image_data['body'];
										$done = true;
									} else {
										$image_data = false;
										$rslb->move_server_list();
										$url = $rslb->get_url('templates', 0, true);
									}

									$count++;
								} while ($done == false && $count < 5);

							} else {
								$count = 0;

								do {
									$image_data = @file_get_contents($url . '/' . $this->templates_server_path . $temp['img']); // Get image data

									if ($image_data == false) {
										$rslb->move_server_list();
										$url = $rslb->get_url('templates', 0, true);
									}

									$count++;
								} while ($image_data == false && $count < 5);

							}

							if ($image_data !== false) {
								$reload[$temp['alias']] = true;
								unset($templates['slider'][$key]['push_image']);

								if (!is_dir(dirname($file))) {
									mkdir(dirname($file), 0777, true);
								}

								@file_put_contents($file, $image_data);

								$loaded = $file;
							}

						}

					}

				}

			}

			if ($loaded === false) {

				if (!empty($templates['slides']) && is_array($templates['slides'])) {

					foreach ($templates['slides'] as $key => $temp) {

						foreach ($temp as $k => $tvalues) {

							if ($img !== false) {
								//we want to download a certain image, check for it

								if ($this->get_val($tvalues, 'img') !== $img) {
									continue;
								}

							}

							// Check folder permission and define file location

							if (RevLoader::wp_mkdir_p($upload_dir['basedir'] . $this->templates_path)) {
								$file = $upload_dir['basedir'] . $this->templates_path . '/' . $tvalues['img'];

								if (!file_exists($file) || isset($reload[$key])) {

									if ($curl !== false) {

										$done = false;
										$count = 0;

										do {

											$image_data = RevLoader::wp_remote_post($url . '/' . $this->templates_server_path . $tvalues['img'], [
												'user-agent' => 'revphp; ' . RevLoader::get_bloginfo('url'),
												'body'       => [
													'item'    => urlencode(RS_PLUGIN_SLUG),
													'version' => urlencode(RS_REVISION),
												],
												'timeout'    => 45,
											]);

											if (!RevLoader::is_wp_error($image_data) && isset($image_data['body']) && isset($image_data['response']) && isset($image_data['response']['code']) && $image_data['response']['code'] == '200') {
												$image_data = $image_data['body'];
												$done = true;
											} else {
												$image_data = false;
												$rslb->move_server_list();
												$url = $rslb->get_url('templates', 0, true);
											}

											$count++;
										} while ($done == false && $count < 5);

									} else {
										$count = 0;

										do {
											$image_data = @file_get_contents($url . '/' . $this->templates_server_path . $tvalues['img']); // Get image data

											if ($image_data == false) {
												$rslb->move_server_list();
												$url = $rslb->get_url('templates', 0, true);
											}

											$count++;
										} while ($image_data == false && $count < 5);

									}

									if ($image_data !== false) {

										if (!is_dir(dirname($file))) {
											mkdir(dirname($file), 0777, true);
										}

										file_put_contents($file, $image_data);
									}

								}

							}

						}

					}

				}

			}

		}

		RevLoader::update_option('rs-templates', $templates, false); //remove the push_image
	}

	/**
	 * Copy a Slide to the Template Slide list
	 * @since: 5.0
	 * @before: RevSliderTemplate::copySlideToTemplates()
	 */
	public function copy_slide_to_templates($slide_id, $slide_title, $slide_settings = []) {

		if (intval($slide_id) == 0) {
			return false;
		}

		$slide_title = RevLoader::sanitize_text_field($slide_title);

		if (strlen(trim($slide_title)) < 3) {
			return false;
		}

		$slide = new RevSliderSlide($slide_id);

		if (empty($slide->id)) {
			return false;
		}

		$duplicate = $slide->duplicateObject();
		$duplicate->id_revslider_slider = -1;
		$duplicate->slide_order = -1;

		$params = json_decode($duplicate['params'], true);
		$settings = json_decode($duplicate['settings'], true);

		$params['title'] = $slide_title;

		if (!isset($params['publish'])) {
			$params['publish'] = [];
		}

		$params['publish']['state'] = 'published';

		if (isset($slide_settings['width'])) {
			$settings['width'] = intval($slide_settings['width']);
		}

		if (isset($slide_settings['height'])) {
			$settings['height'] = intval($slide_settings['height']);
		}

		$duplicate->params = json_encode($params);
		$duplicate->settings = json_encode($settings);

		return $duplicate->update();
	}

	/**
	 * Get all Template Slides
	 * @since: 5.0
	 * @before: RevSliderTemplate::getTemplateSlides();
	 */
	public function get_template_slides() {

		$templates = Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS(
			(new DbQuery())
				->select('*')
				->from('revslider_slide')
				->where('`id_revslider_slide` = -1')
		);

		$default = $this->get_default_template_slides();
		$templates = array_merge($templates, $default);

		if (!empty($templates)) {

			foreach ($templates as $key => $template) {
				$templates[$key]['params'] = json_decode($template['params'], true);
				$templates[$key]['settings'] = json_decode($template['settings'], true);
			}

		}

		return $templates;
	}

	/**
	 * Add default Template Slides that can't be deleted for example. Authors can add their own Slides here through Filter
	 * @since: 5.0
	 * @before: RevSliderTemplate::getDefaultTemplateSlides();
	 */
	private function get_default_template_slides() {

		$templates = [];
		$templates = RevLoader::apply_filters('revslider_set_template_slides', $templates);

		return $templates;
	}

	/**
	 * get default ThemePunch default Slides
	 * @since: 5.0
	 * @before: RevSliderTemplate::getThemePunchTemplateSlides()
	 */
	public function get_tp_template_slides($sliders = false) {

		$templates = [];
		$slide_defaults = [];

		if ($sliders == false) {
			$sliders = $this->get_tp_template_sliders();
		}

		if (!empty($sliders)) {

			foreach ($sliders as $slider) {
				$slides = $this->get_tp_template_default_slides($slider['alias']);
				$installed = false;

				if ($this->get_val($slider, 'installed', false) !== false) {

					$cur_slides = Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS(
						(new DbQuery())
							->select('*')
							->from('revslider_slide')
							->where('`id_revslider_slide` = ' . $slider['installed'])
					);
					$installed = true;
				} else {
					$cur_slides = $slides;
				}

				if (!empty($cur_slides)) {
					$i = 1;

					foreach ($cur_slides as $key => $tmpl) {

						if (isset($slides[$key]) && !empty($slides[$key]['img'])) {
							$cur_slides[$key]['img'] = $this->_check_file_path($slides[$key]['img'], true, false);
						}

						if ($this->get_val($tmpl, 'title', false) === false) {
							$cur_slides[$key]['title'] = 'Slide ' . $i;
						}

						$cur_slides[$key]['uid'] = $this->get_val($slider, 'uid');
						$cur_slides[$key]['parent'] = $this->get_val($slider, 'id');

						if ($installed) {
							$cur_slides[$key]['installed'] = $this->get_val($tmpl, 'id');
						}

						$cur_slides[$key]['plugin_require'] = $this->get_val($slider, 'plugin_require', []);

						$i++;
					}

				}

				$templates = array_merge($templates, $cur_slides);
			}

		}

		if (!empty($templates)) {

			foreach ($templates as $key => $template) {

				if ($this->get_val($template, 'installed', false) !== false) {
					$template['params'] = $this->get_val($template, 'params', '');
					$template['layers'] = $this->get_val($template, 'layers', '');
					$template['settings'] = $this->get_val($template, 'settings', '');

					$templates[$key]['params'] = json_decode($template['params'], true);
					$templates[$key]['settings'] = json_decode($template['settings'], true);
				}

			}

		}

		return $templates;
	}

	/**
	 * get default ThemePunch default Slides
	 * @since: 5.0
	 * @before: RevSliderTemplate::getThemePunchTemplateDefaultSlides()
	 */
	public function get_tp_template_default_slides($slider_alias) {

		$templates = RevLoader::get_option('rs-templates', []);
		$slides = (isset($templates['slides']) && !empty($templates['slides'])) ? $templates['slides'] : [];

		return (isset($slides[$slider_alias])) ? $slides[$slider_alias] : [];
	}

	/**
	 * Get default Template Sliders
	 * @since: 5.0
	 * @before: RevSliderTemplate::getDefaultTemplateSliders();
	 */
	public function get_default_template_sliders() {

		$sliders = [];
		$check = [];

		$check = Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS(
			(new DbQuery())
				->select('*')
				->from('revslider_slider')
				->where('`type` = "template"')
		);

		$sliders = RevLoader::apply_filters('revslider_set_template_sliders', $sliders);

		if (!empty($check) && !empty($sliders)) {

			foreach ($sliders as $key => $the_sliders) {

				foreach ($the_sliders as $skey => $slider) {

					foreach ($check as $ikey => $installed) {

						if ($installed['alias'] == $slider['alias']) {

							$img = $this->get_val($slider, 'img');
							$sliders[$key][$skey] = $installed;
							$sliders[$key][$skey]['img'] = $this->_check_file_path($img, true, false);

							$sliders[$key]['alias'] = $sliders[$key]['alias'];
							$sliders[$key]['version'] = $this->get_val($slider, 'version', '');

							if (isset($slider['is_new'])) {
								$sliders[$key]['is_new'] = true;
							}

							$preview = (isset($slider['preview'])) ? $slider['preview'] : false;

							if ($preview !== false) {
								$sliders[$key]['preview'] = $preview;
							}

							break;
						}

					}

				}

			}

		}

		if (!empty($sliders)) {

			foreach ($sliders as $dk => $slider) {
				$sliders[$dk]['plugin_require'] = json_decode($sliders[$dk]['plugin_require'], true);

				$tags = $sliders[$dk]['filter'];
				$tags[] = $sliders[$dk]['cat'];
				$sliders[$dk]['tags'] = $tags;

				if (!isset($sliders[$dk]['setup_notes'])) {
					$sliders[$dk]['setup_notes'] = '<span class="ttm_content">Checkout our <a href="https://www.themepunch.com/revslider-doc/slider-revolution-documentation/" target="_blank">Documentation</a> for basic Slider Revolution help.</span>';
				}

				unset($sliders[$dk]['filter']);
				unset($sliders[$dk]['cat']);
			}

		}

		return $sliders;
	}

	/**
	 * get default ThemePunch default Sliders
	 * @since: 5.0
	 * @before: RevSliderTemplate::getThemePunchTemplateSliders()
	 */
	public function get_tp_template_sliders($uid = false) {

		$sliders = [];
		$plugin_list = [];

		$sliders = Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS(
			(new DbQuery())
				->select('*')
				->from('revslider_slider')
				->where('`type` = "template"')
		);

		$defaults = RevLoader::get_option('rs-templates', []);
		$defaults = $this->get_val($defaults, 'slider', []);

		if (!empty($sliders) && !empty($defaults)) {

			foreach ($defaults as $key => $slider) {

				if ($uid !== false && $uid !== $this->get_val($slider, 'uid')) {
					unset($defaults[$key]);
					continue;
				}

				foreach ($sliders as $ikey => $installed) {
					$slider = new RevSliderSlider($installed['id_revslider_slider']);

					if ($installed['alias'] == $slider['alias']) {

						//check if $sliders has slides, if not, set for redownload by deleting Template Slider in table
						$c_slides = $this->get_tp_template_slides([$installed]);

						if (empty($c_slides)) {
							$slider->delete();
							break;
						}

						$preview = $this->get_val($slider, 'preview', false);
						$id = $this->get_val($installed, 'id_revslider_slider');
						unset($installed['id']);

						$defaults[$key]['alias'] = $defaults[$key]['alias']; //.'-template'
						$defaults[$key] = array_merge($defaults[$key], $installed);
						$defaults[$key]['installed'] = $id;
						$defaults[$key]['img'] = $this->_check_file_path($slider['img'], true, false);
						$defaults[$key]['version'] = $slider['version'];
						$defaults[$key]['cat'] = $slider['cat'];
						$defaults[$key]['filter'] = $slider['filter'];

						if (isset($slider['is_new'])) {
							$defaults[$key]['is_new'] = true;
							$defaults[$key]['width'] = $slider['width'];
							$defaults[$key]['height'] = $slider['height'];
						}

						$defaults[$key]['zip'] = $slider['zip'];
						$defaults[$key]['uid'] = $slider['uid'];

						if (isset($slider['new_slider'])) {
							$defaults[$key]['new_slider'] = $slider['new_slider'];
						}

						if ($preview !== false) {
							$defaults[$key]['preview'] = $preview;
						}

						break;
					}

				}

			}

			foreach ($defaults as $dk => $di) {
				//check here if package parent needs to be set to installed, as all others

				if (isset($di['package_parent']) && $di['package_parent'] == 'true') {
					$full_installed = true;

					foreach ($defaults as $k => $ps) {

						if ($dk !== $k && isset($ps['package_id']) && $ps['package_id'] === $di['package_id']) {
							//ignore comparing of the same, as it can never be installed
							//if($this->get_val($ps, 'installed') !== false){

							if ($this->get_val($ps, 'installed') === false) {
								$full_installed = false;
								break;
							}

						}

					}

					if ($full_installed) {

						$defaults[$dk]['installed'] = true;
					}

				}

			}

		}

		if (!empty($defaults)) {
			$favorite = new RevSliderFavorite();

			foreach ($defaults as $dk => $default) {

				if ($uid !== false && $uid !== $this->get_val($default, 'uid')) {
					unset($defaults[$dk]);
					continue;
				}

				$defaults[$dk]['plugin_require'] = json_decode($defaults[$dk]['plugin_require'], true);

				if (!empty($defaults[$dk]['plugin_require'])) {

					foreach ($defaults[$dk]['plugin_require'] as $pr => $plugin) {
						$path = $this->get_val($plugin, 'path');

						$path_ex = explode('/', $path);
						$is_active = RevLoader::get_option($path_ex['0']);

						if ($is_active) {
							$plugin_list[$path] = true;
						} else {
							$plugin_list[$path] = false;
						}

						$defaults[$dk]['plugin_require'][$pr]['installed'] = ($plugin_list[$path] === true) ? true : false;
					}

				}

				//$defaults[$dk]['img'] = $this->_check_file_path($defaults[$dk]['img'], true, false);
				$defaults[$dk]['img'] = $defaults[$dk]['img'];
				$tags = $defaults[$dk]['filter'];
				$tags[] = $defaults[$dk]['cat'];
				$defaults[$dk]['tags'] = $tags;
				unset($defaults[$dk]['filter']);
				unset($defaults[$dk]['cat']);

				if (!isset($defaults[$dk]['setup_notes'])) {
					$defaults[$dk]['setup_notes'] = '<span class="ttm_content">Checkout our <a href="https://www.themepunch.com/revslider-doc/slider-revolution-documentation/" target="_blank">Documentation</a> for basic Slider Revolution help.</span>';
				}

				$id = $this->get_val($default, 'id', 0);
				$defaults[$dk]['favorite'] = $favorite->is_favorite('plugintemplates', $id);
			}

		}

		krsort($defaults);

		return $defaults;
	}

	/**
	 * get the template sliders for the get_full_library function
	 * @since: 6.0
	 */
	public function get_tp_template_sliders_for_library($leave_counter = false) {

		$templates = $this->get_tp_template_sliders();
		$addons = [];

		if (!empty($templates)) {

			foreach ($templates as $k => $t) {

				if (isset($templates[$k]['params'])) {
					unset($templates[$k]['params']);
				}

			}

		}

		if (!$this->_truefalse($leave_counter)) {
			RevLoader::update_option('rs-templates-counter', 0, false); //reset the counter
		}

		$templates1 = [];

		foreach ($templates as $k => $t) {

			if (version_compare($t['required'], RS_TP_TOOLS, '>')) {
			} else {

				if ($t['alias'] == 'clean-news-post-based' || $t['alias'] == 'standard-wp-gallery' || $t['alias'] == 'news-gallery-post-based') {
				} else {
					$templates1[] = $t;
				}

			}

		}

		return $templates1;

	}

	/**
	 * get the template slides for the get_full_library function
	 * @since: 6.0
	 */
	public function get_tp_template_slides_for_library($tmp_slide_uid) {

		$tmp_slide_uid = (array) $tmp_slide_uid;

		if (!empty($tmp_slide_uid)) {
			$templates = [];

			foreach ($tmp_slide_uid as $tmp_uid) {
				$templates = $this->get_tp_template_sliders($tmp_uid);
			}

		} else {
			$templates = $this->get_tp_template_sliders();
		}

		$templates_slides = $this->get_tp_template_slides($templates);

		if (!empty($templates_slides)) {

			foreach ($templates_slides as $t_k => $t_slide) {

				if (isset($t_slide['params'])) {
					unset($templates_slides[$t_k]['params']);
				}

				if (isset($t_slide['layers'])) {
					unset($templates_slides[$t_k]['layers']);
				}

				if (isset($t_slide['settings'])) {
					unset($templates_slides[$t_k]['settings']);
				}

			}

		}

		return $templates_slides;
	}

	/**
	 * check if image was uploaded, if yes, return path or url
	 * @since: 5.0.5
	 */
	public function _check_file_path($image, $url = false, $download = true) {

		$upload_dir = RevLoader::wp_upload_dir(); // Set upload folder
		$file = $upload_dir['basedir'] . $this->templates_path . '/' . $image;

		if (file_exists($file)) { //downloaded image first, for update reasons
			$image = ($url) ? $upload_dir['baseurl'] . $this->templates_path . '/' . $image : $upload_dir['basedir'] . $this->templates_path . '/' . $image; //server path
		} else
		if ($download === true) {
			//redownload image from server and store it
			$this->_update_images($image);

			if (file_exists($file)) { //downloaded image first, for update reasons
				$image = ($url) ? $upload_dir['baseurl'] . $this->templates_path . '/' . $image : $upload_dir['basedir'] . $this->templates_path . '/' . $image; //server path
			}

		}

		return $image;
	}

	/**
	 * Get all uids from a certain package, by one uid
	 * @since: 5.2.5
	 */
	public function get_package_uids($uid, $sliders = false) {

		if ($sliders == false) {
			$sliders = $this->get_tp_template_sliders();
		}

		$uids = [];

		$package = false;

		foreach ($sliders as $slider) {

			if ($slider['uid'] == $uid) {

				if (isset($slider['package'])) {
					$package = $slider['package'];
				}

				break;
			}

		}

		if ($package !== false) {
			$i = 0;
			$tuids = [];

			foreach ($sliders as $slider) {

				if (isset($slider['package']) && $slider['package'] == $package) {

					if (isset($slider['package_parent']) && $slider['package_parent'] == 'true') {
						continue;
					}

					//dont install parent package

					if ($this->get_val($slider, 'installed') !== false) {
						//add an invalid slider id as we have not yet installed it
						$i--;
						$sid = $i;
					} else {
						//add the installed slider id, as we have the template installed already
						$sid = $slider['id'];
					}

					$order = (isset($slider['package_order'])) ? $slider['package_order'] : 0;
					$tuids[] = [
						'uid'   => $slider['uid'],
						'sid'   => $sid,
						'order' => $order,
					];
				}

			}

		}

		if (!empty($tuids)) {
			usort($tuids, [$this, 'sort_by_order']);

			foreach ($tuids as $uid) {
				$uids[$uid['sid']] = $uid['uid'];
			}

		}

		return $uids;
	}

	/**
	 * check if Slider Template was already imported. If yes, remove the old Slider Template as we now do an "update" (in reality we delete and insert again)
	 */
	public function remove_old_template($uid) {

		//get all template sliders
		$templates = $this->get_tp_template_sliders();

		foreach ($templates as $tslider) {

			if ($this->get_val($tslider, 'uid') == $uid) {

				if ($this->get_val($tslider, 'installed', false) !== false) {
					//slider is installed
					//delete template Slider!
					$mSlider = new RevSliderSlider();
					$mSlider->init_by_id($tslider['installed']);

					$mSlider->delete_slider();
					//remove the update flag from the slider

					$this->remove_is_new($uid);
				}

				break;
			}

		}

	}

	public function sort_by_order($a, $b) {

		return $a['order'] - $b['order'];
	}

	/**
	 * check if all Slider of a certain package is installed, do this with the uid of a slider
	 * @since: 5.2.5
	 */
	public function check_package_all_installed($uid, $sliders = false) {

		$uids = $this->get_package_uids($uid, $sliders);

		foreach ($uids as $sid => $uid) {

			if ($sid < 0) {
				return false;
			}

		}

		return true;

	}

	/**
	 * Check if Curl can be used
	 */
	public function check_curl_connection() {

		//track
		return true;

		if ($this->curl_check !== null) {
			return $this->curl_check;
		}

		$curl = new WP_Http_Curl();

		$this->curl_check = $curl->test();

		return $this->curl_check;
	}

	/**
	 * get the template existing categories, merging filter and cat
	 **/
	public function get_template_categories() {

		$cat = [];

		$defaults = RevLoader::get_option('rs-templates', []);
		$defaults = $this->get_val($defaults, 'slider', []);

		if (!empty($defaults)) {

			foreach ($defaults as $def) {
				$d_cat = $this->get_val($def, 'cat', '');
				$d_filter = $this->get_val($def, 'filter', []);

				if (trim($d_cat) !== '' && !isset($cat[$d_cat])) {
					$cat[$d_cat] = ucfirst($d_cat);
				}

				if (!empty($d_filter)) {

					foreach ($d_filter as $filter) {

						if (trim($filter) !== '' && !isset($cat[$filter])) {
							$cat[$filter] = ucfirst($filter);
						}

					}

				}

			}

		}

		return $cat;
	}

	/**
	 * get the slide thumbnail
	 **/
	public function get_slide_image_by_uid($uid, $slidenumber) {

		$defaults = RevLoader::get_option('rs-templates', []);
		$sliders = $this->get_val($defaults, 'slider', []);
		$slides = $this->get_val($defaults, 'slides', []);
		$image = false;

		foreach ($sliders as $slider) {

			if ($this->get_val($slider, 'uid') != $uid) {
				continue;
			}

			$alias = $this->get_val($slider, 'alias');
			$slide = $this->get_val($slides, $alias, []);

			if (!empty($slide)) {
				$sl = $this->get_val($slide, $slidenumber, []);
				$image = $this->get_val($sl, 'img');
			}

			break;
		}

		return ($image !== false) ? $this->_check_file_path($image, true, true) : $image;
	}

	/**
	 * get the slide thumbnail
	 **/
	public function get_slider_id_by_uid($uid) {

		$templates = $this->get_tp_template_sliders();
		$slider_id = 0;

		foreach ($templates as $template) {

			if ($this->get_val($template, 'uid') == $uid) {
				$slider_id = $this->get_val($template, 'installed');
				$slider_id = intval($slider_id);
				break;
			}

		}

		return $slider_id;
	}

	/**
	 * clears the uid to make sure no illegal characters are in it
	 **/
	public function clear_uid($uid) {

		return preg_replace("/[^a-zA-Z0-9\s]/", '', $uid);
	}

}

?>