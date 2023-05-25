<?php

class RevSliderFunctionsAdmin extends RevSliderFunction {

	public function get_full_library($include = ['all'], $tmp_slide_uid = [], $refresh_from_server = false, $get_static_slide = false) {

		$include = (array) $include;
		$template = new RevSliderTemplate();
		$library = new RevSliderObjectLibrary();
		$slide = new RevSliderSlide();
		$object = [];
		$tmp_slide_uid = ($tmp_slide_uid !== false) ? (array) $tmp_slide_uid : [];

		if ($refresh_from_server) {

			if (in_array('all', $include) || in_array('plugintemplates', $include)) {
				//refresh template list from server
				$template->_get_template_list(true);

				if (!isset($object['plugintemplates'])) {
					$object['plugintemplates'] = [];
				}

				$object['plugintemplates']['tags'] = $template->get_template_categories();
				asort($object['plugintemplates']['tags']);
			}

			if (in_array('all', $include) || in_array('layers', $include) || in_array('videos', $include) || in_array('images', $include) || in_array('objects', $include)) {
				//refresh object list from server
				$library->_get_list(true);
			}

			if (in_array('all', $include) || in_array('layers', $include)) {
				//refresh object list from server

				if (!isset($object['layers'])) {
					$object['layers'] = [];
				}

				$object['layers']['tags'] = $library->get_objects_categories('4');
				asort($object['layers']['tags']);
			}

			if (in_array('all', $include) || in_array('videos', $include)) {
				//refresh object list from server

				if (!isset($object['videos'])) {
					$object['videos'] = [];
				}

				$object['videos']['tags'] = $library->get_objects_categories('3');
				asort($object['videos']['tags']);
			}

			if (in_array('all', $include) || in_array('images', $include)) {
				//refresh object list from server

				if (!isset($object['images'])) {
					$object['images'] = [];
				}

				$object['images']['tags'] = $library->get_objects_categories('2');
				asort($object['images']['tags']);
			}

			if (in_array('all', $include) || in_array('objects', $include)) {
				//refresh object list from server

				if (!isset($object['objects'])) {
					$object['objects'] = [];
				}

				$object['objects']['tags'] = $library->get_objects_categories('1');
				asort($object['objects']['tags']);
			}

			$object = RevLoader::apply_filters('revslider_get_full_library_refresh', $object, $include, $tmp_slide_uid, $refresh_from_server, $get_static_slide, $this);
		}

		if (in_array('plugintemplates', $include) || in_array('all', $include)) {

			if (!isset($object['plugintemplates'])) {
				$object['plugintemplates'] = [];
			}

			$object['plugintemplates']['items'] = $template->get_tp_template_sliders_for_library($refresh_from_server);
		}

		if (in_array('plugintemplateslides', $include) || in_array('all', $include)) {

			if (!isset($object['plugintemplateslides'])) {
				$object['plugintemplateslides'] = [];
			}

			$object['plugintemplateslides']['items'] = $template->get_tp_template_slides_for_library($tmp_slide_uid);
		}

		if (in_array('plugins', $include) || in_array('all', $include)) {

			if (!isset($object['plugins'])) {
				$object['plugins'] = [];
			}

			$object['plugins']['items'] = $this->get_slider_overview();
		}

		if (in_array('pluginslides', $include) || in_array('all', $include)) {

			if (!isset($object['pluginslides'])) {
				$object['pluginslides'] = [];
			}

			$object['pluginslides']['items'] = $slide->get_slides_for_library($tmp_slide_uid, $get_static_slide);
		}

		if (in_array('svgs', $include) || in_array('all', $include)) {

			if (!isset($object['svgs'])) {
				$object['svgs'] = [];
			}

			$object['svgs']['items'] = $library->get_svg_sets_full();
		}

		if (in_array('fonticons', $include) || in_array('all', $include)) {

			if (!isset($object['fonticons'])) {
				$object['fonticons'] = [];
			}

			$object['fonticons']['items'] = $library->get_font_icons();
		}

		if (in_array('layers', $include) || in_array('all', $include)) {

			if (!isset($object['layers'])) {
				$object['layers'] = [];
			}

			$object['layers']['items'] = $library->load_objects('4');
		}

		if (in_array('videos', $include) || in_array('all', $include)) {

			if (!isset($object['videos'])) {
				$object['videos'] = [];
			}

			$object['videos']['items'] = $library->load_objects('3');
		}

		if (in_array('images', $include) || in_array('all', $include)) {

			if (!isset($object['images'])) {
				$object['images'] = [];
			}

			$object['images']['items'] = $library->load_objects('2');
		}

		if (in_array('objects', $include) || in_array('all', $include)) {

			if (!isset($object['objects'])) {
				$object['objects'] = [];
			}

			$object['objects']['items'] = $library->load_objects('1');
		}

		$object = RevLoader::apply_filters('revslider_get_full_library', $object, $include, $tmp_slide_uid, $refresh_from_server, $get_static_slide, $this);

		return $object;
	}

	public function get_short_library() {

		$template = new RevSliderTemplate();
		$library = new RevSliderObjectLibrary();
		$sliders = $this->get_slider_overview();

		$slider_cat = [];

		if (!empty($sliders)) {

			foreach ($sliders as $slider) {
				$tags = $this->get_val($slider, 'tags', []);

				if (!empty($tags)) {

					foreach ($tags as $tag) {

						if (trim($tag) !== '' && !isset($slider_cat[$tag])) {
							$slider_cat[$tag] = ucwords($tag);
						}

					}

				}

			}

		}

		$svg_cat = $library->get_svg_categories();
		$oc = $library->get_objects_categories('1');
		$oc2 = $library->get_objects_categories('2');
		$oc3 = $library->get_objects_categories('3');
		$oc4 = $library->get_objects_categories('4');
		$t_cat = $template->get_template_categories();
		$font_cat = $library->get_font_tags();

		$wpi = ['jpg' => 'jpg', 'png' => 'png'];
		$wpv = ['mpeg' => 'mpeg', 'mp4' => 'mp4', 'ogv' => 'ogv'];

		asort($wpi);
		asort($wpv);
		asort($oc);
		asort($t_cat);
		asort($slider_cat);
		asort($svg_cat);
		asort($font_cat);

		$tags = [
			'plugintemplates' => ['tags' => $t_cat],
			'plugins'         => ['tags' => $slider_cat],
			'svgs'            => ['tags' => $svg_cat],
			'fonticons'       => ['tags' => $font_cat],
			'layers'          => ['tags' => $oc4],
			'videos'          => ['tags' => $oc3],
			'images'          => ['tags' => $oc2],
			'objects'         => ['tags' => $oc], /*,
			'wpimages'	=> array('tags' => $wpi),
			'wpvideos'	=> array('tags' => $wpv)*/
		];
		return RevLoader::apply_filters('revslider_get_short_library', $tags, $library, $this);
	}

	public function get_slider_overview() {

		$rs_slider = RevSliderSlider::getInstance();
		$rs_slide = RevSliderSlide::getInstance();
		$sliders = $rs_slider->get_sliders(false);

		$rs_folder = new RevSliderFolder();
		$folders = $rs_folder->get_folders();

		$sliders = array_merge($sliders, $folders);
		$data = [];

		if (!empty($sliders)) {
			$slider_list = [];

			foreach ($sliders as $slider) {
				$slider_list[] = $slider->id;
			}

			$slides_raw = $rs_slide->get_all_slides_raw($slider_list);

			foreach ($sliders as $slider) {
				$slides = [];
				$sid = $slider->id;

				foreach ($slides_raw as $s => $r) {

					if ($r->id_revslider_slider !== $sid) {
						continue;
					}

					$slides[] = $r;
					unset($slides_raw[$s]);
				}

				$slides = (empty($slides)) ? false : $slides;

				$slider->init_layer = false;
				$data[] = $slider->get_overview_data(false, $slides);
			}

		}

		return $data;
	}

	public function insert_animation($animation, $type) {

		$handle = $this->get_val($animation, 'name', false);
		$result = false;

		if ($handle !== false && trim($handle) !== '') {
			global $wpdb;

			//check if handle exists
			$arr = [
				'handle'   => $this->get_val($animation, 'name'),
				'params'   => json_encode($animation),
				'settings' => $type,
			];

			$result = $wpdb->insert(_DB_PREFIX_ . RevSliderFront::TABLE_LAYER_ANIMATIONS, $arr);
		}

		return ($result) ? $wpdb->insert_id : $result;
	}

	public function update_animation($animation_id, $animation, $type) {

		global $wpdb;

		$arr = [
			'handle'   => $this->get_val($animation, 'name'),
			'params'   => json_encode($animation),
			'settings' => $type,
		];

		$result = $wpdb->update(_DB_PREFIX_ . RevSliderFront::TABLE_LAYER_ANIMATIONS, $arr, ['id' => $animation_id]);

		return ($result) ? $animation_id : $result;
	}

	/**
	 * delete custom animations
	 * @before: RevSliderOperations::deleteCustomAnim();
	 */
	public function delete_animation($animation_id) {

		global $wpdb;

		$result = $wpdb->delete(_DB_PREFIX_ . RevSliderFront::TABLE_LAYER_ANIMATIONS, ['id' => $animation_id]);

		return $result;
	}

	/**
	 * @since: 5.3.0
	 * create a page with revslider shortcodes included
	 * @before: RevSliderOperations::create_slider_page();
	 **/
	public static function create_slider_page($added, $modals = [], $additions = []) {

		global $wp_version;

		$new_page_id = 0;

		if (!is_array($added)) {
			return RevLoader::apply_filters('revslider_create_slider_page', $new_page_id, $added);
		}

		$content = '';
		$page_id = RevLoader::get_option('rs_import_page_id', 1);

		//get alias of all new Sliders that got created and add them as a shortcode onto a page

		if (!empty($added)) {

			foreach ($added as $sid) {
				$slider = new RevSliderSlider();
				$slider->init_by_id($sid);
				$alias = $slider->get_alias();

				if ($alias !== '') {
					$usage = (in_array($sid, $modals, true)) ? ' usage="modal"' : '';
					$addition = (isset($additions[$sid])) ? ' ' . $additions[$sid] : '';

					if (strpos($addition, 'usage=\"modal\"') !== false) {
						$usage = '';
					}

					//remove as not needed two times

					if (version_compare($wp_version, '5.0', '>=')) {
						//add gutenberg code
						$ov_data = $slider->get_overview_data();
						$title = $slider->get_val($ov_data, 'title', '');
						$img = $slider->get_val($ov_data, ['bg', 'src'], '');
						$wrap_addition = ($img !== '') ? ',"sliderImage":"' . $img . '"' : '';
						$div_addition = ($title !== '') ? ' data-slidertitle="' . $title . '"' : '';

						$zindex_pos = strpos($addition, 'zindex=\"');

						if ($zindex_pos !== false) {
							$zindex = substr($addition, $zindex_pos + 9, strpos($addition, '\"', $zindex_pos + 9) - ($zindex_pos + 9));
							$div_addition .= ' style="z-index:' . $zindex . ';"';
							$wrap_addition .= ',"zindex":"' . $zindex . '"';
						}

						$content .= '<!-- wp:themepunch/revslider {"checked":true' . $wrap_addition . '} -->' . "\n";
						$content .= '<div class="wp-block-themepunch-revslider revslider" data-modal="false"' . $div_addition . '>';
					}

					$content .= '[rev_slider alias="' . $alias . '"' . $usage . $addition . ']'; //this way we will reorder as last comes first

					if (version_compare($wp_version, '5.0', '>=')) {
						//add gutenberg code
						$content .= '</div>' . "\n" . '<!-- /wp:themepunch/revslider -->' . "\n";
					}

				}

			}

		}

		if ($content !== '') {
			$new_page_id = wp_insert_post(
				[
					'post_title'    => wp_strip_all_tags('RevSlider Page ' . $page_id), //$title
					'post_content'  => $content,
					'post_type'     => 'page',
					'post_status'   => 'draft',
					'page_template' => '../public/views/revslider-page-template.php',
				]
			);

			if (is_wp_error($new_page_id)) {
				$new_page_id = 0;
			}

			//fallback to 0

			$page_id++;
			RevLoader::update_option('rs_import_page_id', $page_id);
		}

		return RevLoader::apply_filters('revslider_create_slider_page', $new_page_id, $added);
	}

	/**
	 * add notices from ThemePunch
	 * @since: 4.6.8
	 */
	public function add_notices() {

		$_n = [];
		//track
		//$notices = (array)RevLoader::get_option('revslider-notices', false);
		$notices = RevLoader::get_option('revslider-notices', false);

		if (!empty($notices) && is_array($notices)) {
			$n_discarted = RevLoader::get_option('revslider-notices-dc', []);

			foreach ($notices as $notice) {
				//check if global or just on plugin related pages

				if ($notice->version === true || !in_array($notice->code, $n_discarted) && version_compare($notice->version, RS_REVISION, '>=')) {
					$_n[] = $notice;
				}

			}

		}

		//push whatever notices we might need
		return $_n;
	}

	/**
	 * get basic v5 Slider data
	 **/
	public function get_v5_slider_data() {

		global $wpdb;

		$sliders = [];
		$do_order = 'id';
		$direction = 'ASC';

		$slider_data = Db::getInstance()->executeS(Db::getInstance()->prepare("SELECT `id`, `title`, `alias`, `type` FROM " . _DB_PREFIX_ . RevSliderFront::TABLE_SLIDER . "_bkp ORDER BY %s %s", [$do_order, $direction]), true);

		if (!empty($slider_data)) {

			foreach ($slider_data as $data) {

				if ($this->get_val($data, 'type') == 'template') {
					continue;
				}

				$sliders[] = $data;
			}

		}

		return $sliders;
	}

	/**
	 * get basic v5 Slider data
	 **/
	public function reimport_v5_slider($id) {

		global $wpdb;

		$done = false;

		$slider_data = Db::getInstance()->getRow(Db::getInstance()->prepare("SELECT * FROM " . _DB_PREFIX_ . RevSliderFront::TABLE_SLIDER . "_bkp WHERE `id` = %s", $id), true);

		if (!empty($slider_data)) {
			$slides_data = Db::getInstance()->executeS(Db::getInstance()->prepare("SELECT * FROM " . _DB_PREFIX_ . RevSliderFront::TABLE_SLIDES . "_bkp WHERE `	id_revslider_slider` = %s", $id), true);
			$static_slide_data = Db::getInstance()->getRow(Db::getInstance()->prepare("SELECT * FROM " . _DB_PREFIX_ . RevSliderFront::TABLE_STATIC_SLIDES . "_bkp WHERE `	id_revslider_slider` = %s", $id), true);

			if (!empty($slides_data)) {
				//check if the ID's exist in the new tables, if yes overwrite, if not create
				$slider_v6 = Db::getInstance()->getRow(Db::getInstance()->prepare("SELECT * FROM " . _DB_PREFIX_ . RevSliderFront::TABLE_SLIDER . " WHERE `	id_revslider_slide` = %s", $id), true);
				unset($slider_data['id']);

				if (!empty($slider_v6)) {
					/**
					 * push the old data to the already imported Slider
					 **/
					$result = $wpdb->update(_DB_PREFIX_ . RevSliderFront::TABLE_SLIDER, $slider_data, ['id' => $id]);
				} else {
					$result = $wpdb->insert(_DB_PREFIX_ . RevSliderFront::TABLE_SLIDER, $slider_data);
					$id = ($result) ? $wpdb->insert_id : false;
				}

				if ($id !== false) {

					foreach ($slides_data as $k => $slide_data) {
						$slide_data['slider_id'] = $id;
						$slide_v6 = Db::getInstance()->getRow(Db::getInstance()->prepare("SELECT * FROM " . _DB_PREFIX_ . RevSliderFront::TABLE_SLIDES . " WHERE `id_revslider_static_slide` = %s", $slide_data['id']), true);
						$slide_id = $slide_data['id_revslider_static_slide'];
						unset($slide_data['id_revslider_static_slide']);

						if (!empty($slide_v6)) {
							$result = $wpdb->update(_DB_PREFIX_ . RevSliderFront::TABLE_SLIDES, $slide_data, ['id_revslider_slide' => $slide_id]);
						} else {
							$result = $wpdb->insert(_DB_PREFIX_ . RevSliderFront::TABLE_SLIDES, $slide_data);
						}

					}

					if (!empty($static_slide_data)) {
						$static_slide_data['slider_id'] = $id;
						$slide_v6 = Db::getInstance()->getRow(Db::getInstance()->prepare("SELECT * FROM " . _DB_PREFIX_ . RevSliderFront::TABLE_STATIC_SLIDES . " WHERE `id_revslider_static_slide` = %s", $static_slide_data['id']), true);
						$slide_id = $static_slide_data['id'];
						unset($static_slide_data['id']);

						if (!empty($slide_v6)) {
							$result = $wpdb->update(_DB_PREFIX_ . RevSliderFront::TABLE_STATIC_SLIDES, $static_slide_data, ['id' => $slide_id]);
						} else {
							$result = $wpdb->insert(_DB_PREFIX_ . RevSliderFront::TABLE_STATIC_SLIDES, $static_slide_data);
						}

					}

					$slider = new RevSliderSlider();
					$slider->init_by_id($id);

					$upd = new RevSliderPluginUpdate();

					$upd->upgrade_slider_to_latest($slider);
					$done = true;
				}

			}

		}

		return $done;
	}

	/**
	 * returns an object of current system values
	 **/
	public function get_system_requirements() {

		$dir = RevLoader::wp_upload_dir();
		$basedir = $this->get_val($dir, 'basedir') . '/';
		$ml = ini_get('memory_limit');
		$mlb = RevLoader::wp_convert_hr_to_bytes($ml);
		$umf = ini_get('upload_max_filesize');
		$umfb = RevLoader::wp_convert_hr_to_bytes($umf);
		$pms = ini_get('post_max_size');
		$pmsb = RevLoader::wp_convert_hr_to_bytes($pms);

		$mlg = ($mlb >= 268435456) ? true : false;
		$umfg = ($umfb >= 33554432) ? true : false;
		$pmsg = ($pmsb >= 33554432) ? true : false;

		return [
			'memory_limit'            => [
				'has'  => RevLoader::size_format($mlb),
				'min'  => RevLoader::size_format(268435456),
				'good' => $mlg,
			],
			'upload_max_filesize'     => [
				'has'  => RevLoader::size_format($umfb),
				'min'  => RevLoader::size_format(33554432),
				'good' => $umfg,
			],
			'post_max_size'           => [
				'has'  => RevLoader::size_format($pmsb),
				'min'  => RevLoader::size_format(33554432),
				'good' => $pmsg,
			],
			'upload_folder_writable'  => RevLoader::wp_is_writable($basedir),
			'object_library_writable' => RevLoader::wp_image_editor_supports(['methods' => ['resize', 'save']]),
			'server_connect'          => RevLoader::get_option('revslider-connection', false),
		];
	}

	public function sort_by_slide_order($a, $b) {

		return $a['slide_order'] - $b['slide_order'];
	}

	/**
	 * Create Multilanguage for JavaScript
	 */
	public function get_javascript_multilanguage() {

		$lang = [
			'previewnotworking'                       => $this->l('The preview could not be loaded due to some conflict with another WordPress theme or plugin'),
			'checksystemnotworking'                   => $this->l('Server connection issues, contact your hosting provider for further assistance'),
			'editskins'                               => $this->l('Edit Skin List'),
			'globalcoloractive'                       => $this->l('Color Skin Active'),
			'corejs'                                  => $this->l('Core JavaScript'),
			'corecss'                                 => $this->l('Core CSS'),
			'coretools'                               => $this->l('Core Tools (GreenSock & Co)'),
			'enablecompression'                       => $this->l('Enable Server Compression'),
			'noservercompression'                     => $this->l('Not Available, read FAQ'),
			'servercompression'                       => $this->l('Serverside Compression'),
			'sizeafteroptim'                          => $this->l('Size after Optimization'),
			'chgimgsizesrc'                           => $this->l('Change Image Size or Src'),
			'pickandim'                               => $this->l('Pick another Dimension'),
			'optimize'                                => $this->l('Optimize'),
			'savechanges'                             => $this->l('Save Changes'),
			'applychanges'                            => $this->l('Apply Changes'),
			'suggestion'                              => $this->l('Suggestion'),
			'toosmall'                                => $this->l('Too Small'),
			'standard1x'                              => $this->l('Standard (1x)'),
			'retina2x'                                => $this->l('Retina (2x)'),
			'oversized'                               => $this->l('Oversized'),
			'quality'                                 => $this->l('Quality'),
			'file'                                    => $this->l('File'),
			'resize'                                  => $this->l('Resize'),
			'lowquality'                              => $this->l('Optimized (Low Quality)'),
			'notretinaready'                          => $this->l('Not Retina Ready'),
			'element'                                 => $this->l('Element'),
			'calculating'                             => $this->l('Calculating...'),
			'filesize'                                => $this->l('File Size'),
			'dimension'                               => $this->l('Dimension'),
			'dimensions'                              => $this->l('Dimensions'),
			'optimization'                            => $this->l('Optimization'),
			'optimized'                               => $this->l('Optimized'),
			'smartresize'                             => $this->l('Smart Resize'),
			'optimal'                                 => $this->l('Optimal'),
			'recommended'                             => $this->l('Recommended'),
			'hrecommended'                            => $this->l('Highly Recommended'),
			'optimizertitel'                          => $this->l('File Size Optimizer'),
			'loadedmediafiles'                        => $this->l('Loaded Media Files'),
			'loadedmediainfo'                         => $this->l('Optimize to save up to '),
			'optselection'                            => $this->l('Optimize Selection'),
			'visibility'                              => $this->l('Visibility'),
			'layers'                                  => $this->l('Layers'),
			'videoid'                                 => $this->l('Video ID'),
			'youtubeid'                               => $this->l('YouTube ID'),
			'vimeoid'                                 => $this->l('Vimeo ID'),
			'poster'                                  => $this->l('Poster'),
			'youtubeposter'                           => $this->l('YouTube Poster'),
			'vimeoposter'                             => $this->l('Vimeo Poster'),
			'postersource'                            => $this->l('Poster Image'),
			'medialibrary'                            => $this->l('Media Library'),
			'objectlibrary'                           => $this->l('Object Library'),
			'videosource'                             => $this->l('Video Source'),
			'imagesource'                             => $this->l('Image Source'),
			'extimagesource'                          => $this->l('External Image Source'),
			'mediasrcimage'                           => $this->l('Image Based'),
			'mediasrcext'                             => $this->l('External Image'),
			'mediasrcsolid'                           => $this->l('Background Color'),
			'mediasrctrans'                           => $this->l('Transparent'),
			'please_wait_a_moment'                    => $this->l('Please Wait a Moment'),
			'backgrounds'                             => $this->l('Backgrounds'),
			'name'                                    => $this->l('Name'),
			'colorpicker'                             => $this->l('Color Picker'),
			'savecontent'                             => $this->l('Save Content'),
			'modulbackground'                         => $this->l('Plugin Background'),
			'wrappingtag'                             => $this->l('Wrapping Tag'),
			'tag'                                     => $this->l('Tag'),
			'content'                                 => $this->l('Content'),
			'nolayerstoedit'                          => $this->l('No Layers to Edit'),
			'layermedia'                              => $this->l('Layer Media'),
			'oppps'                                   => $this->l('Ooppps....'),
			'no_nav_changes_done'                     => $this->l('None of the Settings changed. There is Nothing to Save'),
			'no_preset_name'                          => $this->l('Enter Preset Name to Save or Delete'),
			'customlayergrid_size_title'              => $this->l('Custom Size is currently Disabled'),
			'customlayergrid_size_content'            => $this->l('The Current Size is set to calculate the Layer grid sizes Automatically.<br>Do you want to continue with Custom Sizes or do you want to keep the Automatically generated sizes ?'),
			'customlayergrid_answer_a'                => $this->l('Keep Auto Sizes'),
			'customlayergrid_answer_b'                => $this->l('Use Custom Sizes'),
			'removinglayer_title'                     => $this->l('What should happen Next?'),
			'removinglayer_attention'                 => $this->l('Need Attention by removing'),
			'removinglayer_content'                   => $this->l('Where do you want to move the Inherited Layers?'),
			'dragAndDropFile'                         => $this->l('Drag & Drop Import File'),
			'or'                                      => $this->l('or'),
			'clickToChoose'                           => $this->l('Click to Choose'),
			'embed'                                   => $this->l('Embed'),
			'export'                                  => $this->l('Export'),
			'delete'                                  => $this->l('Delete'),
			'duplicate'                               => $this->l('Duplicate'),
			'preview'                                 => $this->l('Preview'),
			'tags'                                    => $this->l('Tags'),
			'folders'                                 => $this->l('Folder'),
			'rename'                                  => $this->l('Rename'),
			'root'                                    => $this->l('Root Level'),
			'simproot'                                => $this->l('Root'),
			'show'                                    => $this->l('Show'),
			'perpage'                                 => $this->l('Per Page'),
			'convertedlayer'                          => $this->l('Layer converted Successfully'),
			'layerloopdisabledduetimeline'            => $this->l('Layer Loop Effect disabled'),
			'layerbleedsout'                          => $this->l('<b>Layer width bleeds out of Grid:</b><br>-Auto Layer width has been removed<br>-Line Break set to Content Based'),
			'noMultipleSelectionOfLayers'             => $this->l('Multiple Layerselection not Supported<br>in Animation Mode'),
			'closeNews'                               => $this->l('Close News'),
			'copyrightandlicenseinfo'                 => $this->l('&copy; Copyright & License Info'),
			'registered'                              => $this->l('Registered'),
			'notRegisteredNow'                        => $this->l('Unregistered'),
			'dismissmessages'                         => $this->l('Dismiss Messages'),
			'someAddonnewVersionAvailable'            => $this->l('Some AddOns have new versions available'),
			'newVersionAvailable'                     => $this->l('New Version Available. Please Update'),
			'addonsmustbeupdated'                     => $this->l('AddOns Outdated. Please Update'),
			'notRegistered'                           => $this->l('Plugin is not Registered'),
			'notRegNoPremium'                         => $this->l('Register to unlock Premium Features'),
			'notRegNoAll'                             => $this->l('Register to Unlock all Features'),
			'notRegNoAddOns'                          => $this->l('Register to unlock AddOns'),
			'notRegNoSupport'                         => $this->l('Register to unlock Support'),
			'notRegNoLibrary'                         => $this->l('Register to unlock Library'),
			'notRegNoUpdates'                         => $this->l('Register to unlock Updates'),
			'notRegNoTemplates'                       => $this->l('Register to unlock Templates'),
			'areyousureupdateplugin'                  => $this->l('Do you want to start the Update process?'),
			'updatenow'                               => $this->l('Update Now'),
			'toplevels'                               => $this->l('Higher Level'),
			'siblings'                                => $this->l('Current Level'),
			'otherfolders'                            => $this->l('Other Folders'),
			'parent'                                  => $this->l('Parent Level'),
			'from'                                    => $this->l('from'),
			'to'                                      => $this->l('to'),
			'actionneeded'                            => $this->l('Action Needed'),
			'updatedoneexist'                         => $this->l('Done'),
			'updateallnow'                            => $this->l('Update All'),
			'updatelater'                             => $this->l('Update Later'),
			'addonsupdatemain'                        => $this->l('The following AddOns require an update:'),
			'addonsupdatetitle'                       => $this->l('AddOns need attention'),
			'updatepluginfailed'                      => $this->l('Updating Plugin Failed'),
			'updatingplugin'                          => $this->l('Updating Plugin...'),
			'licenseissue'                            => $this->l('License validation issue Occured. Please contact our Support.'),
			'leave'                                   => $this->l('Back to Overview'),
			'reLoading'                               => $this->l('Page is reloading...'),
			'updateplugin'                            => $this->l('Update Plugin'),
			'updatepluginsuccess'                     => $this->l('Slider Revolution Plugin updated Successfully.'),
			'updatepluginfailure'                     => $this->l('Slider Revolution Plugin updated Failure:'),
			'updatepluginsuccesssubtext'              => $this->l('Slider Revolution Plugin updated Successfully to'),
			'reloadpage'                              => $this->l('Reload Page'),
			'loading'                                 => $this->l('Loading'),
			'globalcolors'                            => $this->l('Global Colors'),
			'elements'                                => $this->l('Elements'),
			'loadingthumbs'                           => $this->l('Loading Thumbnails...'),
			'jquerytriggered'                         => $this->l('jQuery Triggered'),
			'atriggered'                              => $this->l('&lt;a&gt; Tag Link'),
			'firstslide'                              => $this->l('First Slide'),
			'lastslide'                               => $this->l('Last Slide'),
			'nextslide'                               => $this->l('Next Slide'),
			'previousslide'                           => $this->l('Previous Slide'),
			'somesourceisnotcorrect'                  => $this->l('Some Settings in Slider <strong>Source may not complete</strong>.<br>Please Complete All Settings in Slider Sources.'),
			'somelayerslocked'                        => $this->l('Some Layers are <strong>Locked</strong> and/or <strong>Invisible</strong>.<br>Change Status in Timeline.'),
			'editorisLoading'                         => $this->l('Editor is Loading...'),
			'addingnewblankplugin'                    => $this->l('Adding new Blank Plugin...'),
			'opening'                                 => $this->l('Opening'),
			'featuredimages'                          => $this->l('Featured Images'),
			'images'                                  => $this->l('Images'),
			'none'                                    => $this->l('None'),
			'select'                                  => $this->l('Select'),
			'reset'                                   => $this->l('Reset'),
			'custom'                                  => $this->l('Custom'),
			'out'                                     => $this->l('OUT'),
			'in'                                      => $this->l('IN'),
			'sticky_navigation'                       => $this->l('Navigation Options'),
			'sticky_slider'                           => $this->l('Plugin General Options'),
			'sticky_slide'                            => $this->l('Slide Options'),
			'sticky_layer'                            => $this->l('Layer Options'),
			'imageCouldNotBeLoaded'                   => $this->l('Set a Slide Background Image to use this feature'),
			'oppps'                                   => $this->l('Ooppps....'),
			'no_nav_changes_done'                     => $this->l('None of the Settings changed. There is Nothing to Save'),
			'no_preset_name'                          => $this->l('Enter Preset Name to Save or Delete'),
			'customlayergrid_size_title'              => $this->l('Custom Size is currently Disabled'),
			'customlayergrid_size_content'            => $this->l('The Current Size is set to calculate the Layer grid sizes Automatically.<br>Do you want to continue with Custom Sizes or do you want to keep the Automatically generated sizes ?'),
			'customlayergrid_answer_a'                => $this->l('Keep Auto Sizes'),
			'customlayergrid_answer_b'                => $this->l('Use Custom Sizes'),
			'removinglayer_title'                     => $this->l('What should happen Next?'),
			'removinglayer_attention'                 => $this->l('Need Attention by removing'),
			'removinglayer_content'                   => $this->l('Where do you want to move the Inherited Layers?'),
			'dragAndDropFile'                         => $this->l('Drag & Drop Import File'),
			'or'                                      => $this->l('or'),
			'clickToChoose'                           => $this->l('Click to Choose'),
			'embed'                                   => $this->l('Embed'),
			'export'                                  => $this->l('Export'),
			'exporthtml'                              => $this->l('HTML'),
			'delete'                                  => $this->l('Delete'),
			'duplicate'                               => $this->l('Duplicate'),
			'preview'                                 => $this->l('Preview'),
			'tags'                                    => $this->l('Tags'),
			'folders'                                 => $this->l('Folder'),
			'rename'                                  => $this->l('Rename'),
			'root'                                    => $this->l('Root Level'),
			'simproot'                                => $this->l('Root'),
			'show'                                    => $this->l('Show'),
			'perpage'                                 => $this->l('Per Page'),
			'releaseToAddLayer'                       => $this->l('Release to Add Layer'),
			'releaseToUpload'                         => $this->l('Release to Upload file'),
			'pluginZipFile'                           => $this->l('Plugin .zip'),
			'importing'                               => $this->l('Processing Import of'),
			'importfailure'                           => $this->l('An Error Occured while importing'),
			'successImportFile'                       => $this->l('File Succesfully Imported'),
			'importReport'                            => $this->l('Import Report'),
			'updateNow'                               => $this->l('Update Now'),
			'activateToUpdate'                        => $this->l('Activate To Update'),
			'activated'                               => $this->l('Activated'),
			'notActivated'                            => $this->l('Not Activated'),
			'embedingLine1'                           => $this->l('Standard Plugin Embedding'),
			'embedingLine2'                           => $this->l('For the <b>pages and posts</b> editor insert the Shortcode:'),
			'embedingLine2a'                          => $this->l('To Use it as <b>Modal</b> on <b>pages and posts</b> editor insert the Shortcode:'),
			'embedingLine3'                           => $this->l('From the <b>widgets panel</b> drag the "Revolution Plugin" widget to the desired sidebar.'),
			'embedingLine4'                           => $this->l('Advanced Plugin Embedding'),
			'embedingLine5'                           => $this->l('For the <b>theme html</b> use:'),
			'embedingLine6'                           => $this->l('To add the slider only to the homepage, use:'),
			'embedingLine7'                           => $this->l('To add the slider only to single Pages, use:'),
			'noLayersSelected'                        => $this->l('Select a Layer'),
			'layeraction_group_link'                  => $this->l('Link Actions'),
			'layeraction_group_slide'                 => $this->l('Slide Actions'),
			'layeraction_group_layer'                 => $this->l('Layer Actions'),
			'layeraction_group_media'                 => $this->l('Media Actions'),
			'layeraction_group_fullscreen'            => $this->l('Fullscreen Actions'),
			'layeraction_group_advanced'              => $this->l('Advanced Actions'),
			'layeraction_menu'                        => $this->l('Menu Link & Scroll'),
			'layeraction_link'                        => $this->l('Simple Link'),
			'layeraction_callback'                    => $this->l('Call Back'),
			'layeraction_modal'                       => $this->l('Open Slider Modal'),
			'layeraction_scroll_under'                => $this->l('Scroll below Slider'),
			'layeraction_scrollto'                    => $this->l('Scroll To ID'),
			'layeraction_jumpto'                      => $this->l('Jump to Slide'),
			'layeraction_next'                        => $this->l('Next Slide'),
			'layeraction_prev'                        => $this->l('Previous Slide'),
			'layeraction_next_frame'                  => $this->l('Next Frame'),
			'layeraction_prev_frame'                  => $this->l('Previous Frame'),
			'layeraction_pause'                       => $this->l('Pause Slider'),
			'layeraction_resume'                      => $this->l('Play Slide'),
			'layeraction_close_modal'                 => $this->l('Close Slider Modal'),
			'layeraction_open_modal'                  => $this->l('Open Slider Modal'),
			'layeraction_toggle_slider'               => $this->l('Toggle Slider'),
			'layeraction_start_in'                    => $this->l('Go to 1st Frame '),
			'layeraction_start_out'                   => $this->l('Go to Last Frame'),
			'layeraction_start_frame'                 => $this->l('Go to Frame "N"'),
			'layeraction_toggle_layer'                => $this->l('Toggle 1st / Last Frame'),
			'layeraction_toggle_frames'               => $this->l('Toggle "N/M" Frames'),
			'layeraction_start_video'                 => $this->l('Start Media'),
			'layeraction_stop_video'                  => $this->l('Stop Media'),
			'layeraction_toggle_video'                => $this->l('Toggle Media'),
			'layeraction_mute_video'                  => $this->l('Mute Media'),
			'layeraction_unmute_video'                => $this->l('Unmute Media'),
			'layeraction_toggle_mute_video'           => $this->l('Toggle Mute Media'),
			'layeraction_toggle_global_mute_video'    => $this->l('Toggle Mute All Media'),
			'layeraction_togglefullscreen'            => $this->l('Toggle Fullscreen'),
			'layeraction_gofullscreen'                => $this->l('Enter Fullscreen'),
			'layeraction_exitfullscreen'              => $this->l('Exit Fullscreen'),
			'layeraction_simulate_click'              => $this->l('Simulate Click'),
			'layeraction_toggle_class'                => $this->l('Toggle Class'),
			'layeraction_none'                        => $this->l('Disabled'),
			'backgroundvideo'                         => $this->l('Background Video'),
			'videoactiveslide'                        => $this->l('Video in Active Slide'),
			'firstvideo'                              => $this->l('Video in Active Slide'),
			'triggeredby'                             => $this->l('Behavior'),
			'addaction'                               => $this->l('Add Action to '),
			'ol_images'                               => $this->l('Images'),
			'ol_layers'                               => $this->l('Layer Objects'),
			'ol_objects'                              => $this->l('Objects'),
			'ol_plugins'                              => $this->l('Own Plugins'),
			'ol_fonticons'                            => $this->l('Font Icons'),
			'ol_plugintemplates'                      => $this->l('Plugin Templates'),
			'ol_videos'                               => $this->l('Videos'),
			'ol_svgs'                                 => $this->l('SVG\'s'),
			'ol_favorite'                             => $this->l('Favorites'),
			'installed'                               => $this->l('Installed'),
			'notinstalled'                            => $this->l('Not Installed'),
			'setupnotes'                              => $this->l('Setup Notes'),
			'requirements'                            => $this->l('Requirements'),
			'installedversion'                        => $this->l('Installed Version'),
			'cantpulllinebreakoutside'                => $this->l('Use LineBreaks only in Columns'),
			'availableversion'                        => $this->l('Available Version'),
			'installpackage'                          => $this->l('Installing Template Package'),
			'installtemplate'                         => $this->l('Install Template'),
			'installingtemplate'                      => $this->l('Installing Template'),
			'search'                                  => $this->l('Search'),
			'publish'                                 => $this->l('Publish'),
			'unpublish'                               => $this->l('Unpublish'),
			'slidepublished'                          => $this->l('Slide Published'),
			'slideunpublished'                        => $this->l('Slide Unpublished'),
			'layerpublished'                          => $this->l('Layer Published'),
			'layerunpublished'                        => $this->l('Layer Unpublished'),
			'folderBIG'                               => $this->l('FOLDER'),
			'pluginBIG'                               => $this->l('PLUGIN'),
			'objectBIG'                               => $this->l('OBJECT'),
			'packageBIG'                              => $this->l('PACKAGE'),
			'thumbnail'                               => $this->l('Thumbnail'),
			'imageBIG'                                => $this->l('IMAGE'),
			'videoBIG'                                => $this->l('VIDEO'),
			'iconBIG'                                 => $this->l('ICON'),
			'svgBIG'                                  => $this->l('SVG'),
			'fontBIG'                                 => $this->l('FONT'),
			'redownloadTemplate'                      => $this->l('Re-Download Online'),
			'createBlankPage'                         => $this->l('Create Blank Page'),
			'please_wait_a_moment'                    => $this->l('Please Wait a Moment'),
			'changingscreensize'                      => $this->l('Changing Screen Size'),
			'qs_headlines'                            => $this->l('Headlines'),
			'qs_content'                              => $this->l('Content'),
			'qs_buttons'                              => $this->l('Buttons'),
			'qs_bgspace'                              => $this->l('BG & Space'),
			'qs_shadow'                               => $this->l('Shadow'),
			'qs_shadows'                              => $this->l('Shadow'),
			'saveslide'                               => $this->l('Saving Slide'),
			'loadconfig'                              => $this->l('Loading Configuration'),
			'updateselects'                           => $this->l('Updating Lists'),
			'lastslide'                               => $this->l('Last Slide'),
			'textlayers'                              => $this->l('Text Layers'),
			'globalLayers'                            => $this->l('Global Layers'),
			'slidersettings'                          => $this->l('Slider Settings'),
			'animatefrom'                             => $this->l('Animate From'),
			'animateto'                               => $this->l('Keyframe #'),
			'transformidle'                           => $this->l('Transform Idle'),
			'enterstage'                              => $this->l('Anim From'),
			'leavestage'                              => $this->l('Anim To'),
			'onstage'                                 => $this->l('Anim To'),
			'keyframe'                                => $this->l('Keyframe'),
			'notenoughspaceontimeline'                => $this->l('Not Enough space between Frames.'),
			'framesizecannotbeextended'               => $this->l('Frame Size can not be Extended. Not enough Space.'),
			'backupTemplateLoop'                      => $this->l('Loop Template'),
			'backupTemplateLayerAnim'                 => $this->l('Animation Template'),
			'choose_image'                            => $this->l('Choose Image'),
			'choose_video'                            => $this->l('Choose Video'),
			'slider_revolution_shortcode_creator'     => $this->l('Slider Revolution Shortcode Creator'),
			'shortcode_generator'                     => $this->l('Shortcode Generator'),
			'please_add_at_least_one_layer'           => $this->l('Please add at least one Layer.'),
			'shortcode_parsing_successfull'           => $this->l('Shortcode parsing successfull. Items can be found in step 3'),
			'shortcode_could_not_be_correctly_parsed' => $this->l('Shortcode could not be parsed.'),
			'addonrequired'                           => $this->l('Addon Required'),
			'licencerequired'                         => $this->l('Activate License'),
			'searcforicon'                            => $this->l('Search Icons...'),
			'savecurrenttemplate'                     => $this->l('Save Current Template'),
			'overwritetemplate'                       => $this->l('Overwrite Template ?'),
			'deletetemplate'                          => $this->l('Delete Template ?'),
			'credits'                                 => $this->l('Credits'),
			'notinstalled'                            => $this->l('Not Installed'),
			'enabled'                                 => $this->l('Enabled'),
			'global'                                  => $this->l('Global'),
			'install_and_activate'                    => $this->l('Install Add-On'),
			'install'                                 => $this->l('Install'),
			'enableaddon'                             => $this->l('Enable Add-On'),
			'disableaddon'                            => $this->l('Disable Add-On'),
			'enableglobaladdon'                       => $this->l('Enable Global Add-On'),
			'disableglobaladdon'                      => $this->l('Disable Global Add-On'),
			'sliderrevversion'                        => $this->l('Slider Revolution Version'),
			'checkforrequirements'                    => $this->l('Check Requirements'),
			'activateglobaladdon'                     => $this->l('Activate Global Add-On'),
			'activateaddon'                           => $this->l('Activate Add-On'),
			'activatingaddon'                         => $this->l('Activating Add-On'),
			'enablingaddon'                           => $this->l('Enabling Add-On'),
			'addon'                                   => $this->l('Add-On'),
			'installingaddon'                         => $this->l('Installing Add-On'),
			'disablingaddon'                          => $this->l('Disabling Add-On'),
			'buildingSelects'                         => $this->l('Building Select Boxes'),
			'warning'                                 => $this->l('Warning'),
			'blank_page_added'                        => $this->l('Blank Page Created'),
			'blank_page_created'                      => $this->l('Blank page has been created:'),
			'visit_page'                              => $this->l('Visit Page'),
			'edit_page'                               => $this->l('Edit Page'),
			'closeandstay'                            => $this->l('Close'),
			'changesneedreload'                       => $this->l('The changes you made require a page reload!'),
			'saveprojectornot '                       => $this->l('Save your project & reload the page or cancel'),
			'saveandreload'                           => $this->l('Save & Reload'),
			'canceldontreload'                        => $this->l('Cancel & Reload Later'),
			'saveconfig'                              => $this->l('Save Configuration'),
			'updatingaddon'                           => $this->l('Updating'),
			'addonOnlyInSlider'                       => $this->l('Enable/Disable Add-On on Plugin'),
			'openQuickEditor'                         => $this->l('Open Quick Content Editor'),
			'openQuickStyleEditor'                    => $this->l('Open Quick Style Editor'),
			'sortbycreation'                          => $this->l('Sort by Creation'),
			'creationascending'                       => $this->l('Creation Ascending'),
			'sortbytitle'                             => $this->l('Sort by Title'),
			'titledescending'                         => $this->l('Title Descending'),
			'updatefromserver'                        => $this->l('Update List'),
			'audiolibraryloading'                     => $this->l('Audio Wave Library is Loading...'),
			'editPlugin'                              => $this->l('Edit Plugin'),
			'editSlide'                               => $this->l('Edit Slide'),
			'showSlides'                              => $this->l('Show Slides'),
			'openInEditor'                            => $this->l('Open in Editor'),
			'openFolder'                              => $this->l('Open Folder'),
			'moveToFolder'                            => $this->l('Move to Folder'),
			'loadingcodemirror'                       => $this->l('Loading CodeMirror Library...'),
			'lockunlocklayer'                         => $this->l('Lock / Unlock Selected'),
			'nrlayersimporting'                       => $this->l('Layers Importing'),
			'nothingselected'                         => $this->l('Nothing Selected'),
			'layerwithaction'                         => $this->l('Layer with Action'),
			'imageisloading'                          => $this->l('Image is Loading...'),
			'importinglayers'                         => $this->l('Importing Layers...'),
			'triggeredby'                             => $this->l('Triggered By'),
			'import'                                  => $this->l('Imported'),
			'layersBIG'                               => $this->l('LAYERS'),
			'intinheriting'                           => $this->l('Responsivity'),
			'changesdone_exit'                        => $this->l('The changes you made will be lost!'),
			'exitwihoutchangesornot'                  => $this->l('Are you sure you want to continue?'),
			'areyousuretoexport'                      => $this->l('Are you sure you want to export '),
			'areyousuretodelete'                      => $this->l('Are you sure you want to delete '),
			'areyousuretodeleteeverything'            => $this->l('Delete All Sliders and Folders included in '),
			'leavewithoutsave'                        => $this->l('Leave without Save'),
			'updatingtakes'                           => $this->l('Updating the Plugin may take a few moments.'),
			'exportslidertxt'                         => $this->l('Downloading the Zip File may take a few moments.'),
			'exportslider'                            => $this->l('Export Slider'),
			'yesexport'                               => $this->l('Yes, Export Slider'),
			'yesdelete'                               => $this->l('Yes, Delete Slider'),
			'yesdeleteslide'                          => $this->l('Yes, Delete Slide'),
			'yesdeleteall'                            => $this->l('Yes, Delete All Slider(s)'),
			'stayineditor'                            => $this->l('Stay in Edior'),
			'redirectingtooverview'                   => $this->l('Redirecting to Overview Page'),
			'leavingpage'                             => $this->l('Leaving current Page'),
			'ashtmlexport'                            => $this->l('as HTML Document'),
			'preparingdatas'                          => $this->l('Preparing Data...'),
			'loadingcontent'                          => $this->l('Loading Content...'),
			'copy'                                    => $this->l('Copy'),
			'paste'                                   => $this->l('Paste'),
			'framewait'                               => $this->l('WAIT'),
			'frstframe'                               => $this->l('1st Frame'),
			'lastframe'                               => $this->l('Last Frame'),
			'onlyonaction'                            => $this->l('on Action'),
			'cannotbeundone'                          => $this->l('This action can not be undone !!'),
			'deleteslider'                            => $this->l('Delete Slider'),
			'deleteslide'                             => $this->l('Delete Slide'),
			'deletingslide'                           => $this->l('This can be Undone only within the Current session.'),
			'deleteselectedslide'                     => $this->l('Are you sure you want to delete the selected Slide:'),
			'cancel'                                  => $this->l('Cancel'),
			'addons'                                  => $this->l('Add-Ons'),
			'deletingsingleslide'                     => $this->l('Deleting Slide'),
			'lastslidenodelete'                       => $this->l('"Last Slide in Plugin. Can not be deleted"'),
			'deletingslider'                          => $this->l('Deleting Slider'),
			'active_sr_tmp_obl'                       => $this->l('Template & Object Library'),
			'active_sr_inst_upd'                      => $this->l('Instant Updates'),
			'active_sr_one_on_one'                    => $this->l('1on1 Support'),
			'parallaxsettoenabled'                    => $this->l('Parallax is now generally Enabled'),
			'timelinescrollsettoenabled'              => $this->l('Scroll Based Timeline is now generally Enabled'),
			'feffectscrollsettoenabled'               => $this->l('Filter Effect Scroll is now generally Enabled'),
			'nolayersinslide'                         => $this->l('Slide has no Layers'),
			'leaving'                                 => $this->l('Changes that you made may not be saved.'),
			'sliderasmodal'                           => $this->l('Add Slider as Modal'),
			'register_to_unlock'                      => $this->l('Register to unlock all Premium Features'),
			'premium_features_unlocked'               => $this->l('All Premium Features unlocked'),
			'tryagainlater'                           => $this->l('Please try again later'),
			'quickcontenteditor'                      => $this->l('Quick Content Editor'),
			'plugin'                                  => $this->l('Plugin'),
			'quickstyleeditor'                        => $this->l('Quick Style Editor'),
			'all'                                     => $this->l('All'),
			'active_sr_to_access'                     => $this->l('Register Slider Revolution<br>to Unlock Premium Features'),
			'membersarea'                             => $this->l('Members Area'),
			'onelicensekey'                           => $this->l('1 License Key per Website!'),
			'onepurchasekey'                          => $this->l('1 Purchase Code per Website!'),
			'onelicensekey_info'                      => $this->l('If you want to use your license key on another domain, please<br> deregister it in the members area or use a different key.'),
			'onepurchasekey_info'                     => $this->l('If you want to use your purchase code on<br>another domain, please deregister it first or'),
			'registeredlicensekey'                    => $this->l('Registered License Key'),
			'registeredpurchasecode'                  => $this->l('Registered Purchase Code'),
			'registerlicensekey'                      => $this->l('Register License Key'),
			'registerpurchasecode'                    => $this->l('Register Purchase Code'),
			'registerCode'                            => $this->l('Register this Code'),
			'registerKey'                             => $this->l('Register this License Key'),
			'deregisterCode'                          => $this->l('Deregister this Code'),
			'deregisterKey'                           => $this->l('Deregister this License Key'),
			'active_sr_plg_activ'                     => $this->l('Register Purchase Code'),
			'active_sr_plg_activ_key'                 => $this->l('Register License Key'),
			'getpurchasecode'                         => $this->l('Get a Purchase Code'),
			'getlicensekey'                           => $this->l('Licensing Options'),
			'ihavepurchasecode'                       => $this->l('I have a Purchase Code'),
			'ihavelicensekey'                         => $this->l('I have a License Key'),
			'enterlicensekey'                         => $this->l('Enter License Key'),
			'enterpurchasecode'                       => $this->l('Enter Purchase Code'),
			'colrskinhas'                             => $this->l('This Skin use'),
			'deleteskin'                              => $this->l('Delete Skin'),
			'references'                              => $this->l('References'),
			'colorwillkept'                           => $this->l('The References will keep their colors after deleting Skin.'),
			'areyousuredeleteskin'                    => $this->l('Are you sure to delete Color Skin?'),

		];

		return RevLoader::apply_filters('revslider_get_javascript_multilanguage', $lang);
	}

	/**
	 * returns all image sizes that have the same aspect ratio, rounded on the second
	 * @since: 6.1.4
	 **/
	public function get_same_aspect_ratio_images($images) {

		$return = [];
		$images = (array) $images;

		if (!empty($images)) {
			$objlib = new RevSliderObjectLibrary();
			$upload_dir = RevLoader::wp_upload_dir();

			foreach ($images as $key => $image) {
				//check if we are from object library

				if ($objlib->_is_object($image)) {
					$_img = $image;
					$image = $objlib->get_correct_size_url($image, 100, true);
					$objlib->_check_object_exist($image); //check to redownload if not downloaded yet

					$sizes = $objlib->get_sizes();
					$return[$key] = [];

					if (!empty($sizes)) {

						foreach ($sizes as $size) {
							$url = $objlib->get_correct_size_url($image, $size);
							$file = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $url);
							$_size = getimagesize($file);
							$return[$key][$size] = [
								'url'    => $url,
								'width'  => $this->get_val($_size, 0),
								'height' => $this->get_val($_size, 1),
								'size'   => filesize($file),
							];

							if ($_img === $url) {
								$return[$key][$size]['default'] = true;
							}

						}

						//$image = $objlib->get_correct_size_url($image, 100, true);
						$file = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $image);
						$_size = getimagesize($file);
						$return[$key][100] = [
							'url'    => $image,
							'width'  => $this->get_val($_size, 0),
							'height' => $this->get_val($_size, 1),
							'size'   => filesize($file),
						];

						if ($_img === $return[$key][100]['url']) {
							$return[$key][100]['default'] = true;
						}

					}

				} else {
					$_img = ((int) ($image) === 0) ? $this->get_image_id_by_url($image) : $image;
					//track
					//$img_data = wp_get_attachment_metadata($_img);
					$img_data = '';

					if (!empty($img_data)) {
						$return[$key] = [];
						$ratio = round($this->get_val($img_data, 'width', 1) / $this->get_val($img_data, 'height', 1), 2);
						$sizes = $this->get_val($img_data, 'sizes', []);
						$file = $upload_dir['basedir'] . '/' . $this->get_val($img_data, 'file');
						$return[$key]['orig'] = [
							'url'    => $upload_dir['baseurl'] . '/' . $this->get_val($img_data, 'file'),
							'width'  => $this->get_val($img_data, 'width'),
							'height' => $this->get_val($img_data, 'height'),
							'size'   => filesize($file),
						];

						if ($image === $return[$key]['orig']['url']) {
							$return[$key]['orig']['default'] = true;
						}

						if (!empty($sizes)) {

							foreach ($sizes as $sn => $sv) {
								$_ratio = round($this->get_val($sv, 'width', 1) / $this->get_val($sv, 'height', 1), 2);

								if ($_ratio === $ratio) {
									$i = wp_get_attachment_image_src($_img, $sn);

									if ($i === false) {
										continue;
									}

									$file = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $this->get_val($i, 0));
									$return[$key][$sn] = [
										'url'    => $this->get_val($i, 0),
										'width'  => $this->get_val($sv, 'width'),
										'height' => $this->get_val($sv, 'height'),
										'size'   => filesize($file),
									];

									if ($image === $return[$key][$sn]['url']) {
										$return[$key][$sn]['default'] = true;
									}

								}

							}

						}

					} else {
						//either external URL or not available anymore in the media library
					}

				}

			}

		}

		return $return;
	}

	/**
	 * returns all files plus sizes of JavaScript and css files used by the AddOns
	 * @since. 6.1.4
	 **/
	public function get_addon_sizes($addons) {

		$sizes = [];

		if (empty($addons) || !is_array($addons)) {
			return $sizes;
		}

		$_css = '/public/assets/css/';
		$_js = '/public/assets/js/';
		//these are the sizes before the AddOns where updated
		$_a = [
			'revslider-404-addon'             => [],
			'revslider-backup-addon'          => [],
			'revslider-beforeafter-addon'     => [
				$_css . 'revolution.addon.beforeafter.css'   => 3512,
				$_js . 'revolution.addon.beforeafter.min.js' => 21144,
			],
			'revslider-bubblemorph-addon'     => [
				$_css . 'revolution.addon.bubblemorph.css'   => 341,
				$_js . 'revolution.addon.bubblemorph.min.js' => 11377,
			],
			'revslider-domain-switch-addon'   => [],
			'revslider-duotonefilters-addon'  => [
				$_css . 'revolution.addon.duotone.css'   => 11298,
				$_js . 'revolution.addon.duotone.min.js' => 1232,
			],
			'revslider-explodinglayers-addon' => [
				$_css . 'revolution.addon.explodinglayers.css'   => 704,
				$_js . 'revolution.addon.explodinglayers.min.js' => 19012,
			],
			'revslider-featured-addon'        => [],
			'revslider-filmstrip-addon'       => [
				$_css . 'revolution.addon.filmstrip.css'   => 843,
				$_js . 'revolution.addon.filmstrip.min.js' => 5409,
			],
			'revslider-gallery-addon'         => [],
			'revslider-liquideffect-addon'    => [
				$_css . 'revolution.addon.liquideffect.css'   => 606,
				$_js . 'pixi.min.js'                          => 514062,
				$_js . 'revolution.addon.liquideffect.min.js' => 11899,
			],
			'revslider-login-addon'           => [],
			'revslider-maintenance-addon'     => [],
			'revslider-paintbrush-addon'      => [
				$_css . 'revolution.addon.paintbrush.css'   => 676,
				$_js . 'revolution.addon.paintbrush.min.js' => 6841,
			],
			'revslider-panorama-addon'        => [
				$_css . 'revolution.addon.panorama.css'   => 1823,
				$_js . 'three.min.js'                     => 504432,
				$_js . 'revolution.addon.panorama.min.js' => 12909,
			],
			'revslider-particles-addon'       => [
				$_css . 'revolution.addon.particles.css'   => 668,
				$_js . 'revolution.addon.particles.min.js' => 33963,
			],
			'revslider-polyfold-addon'        => [
				$_css . 'revolution.addon.polyfold.css'   => 900,
				$_js . 'revolution.addon.polyfold.min.js' => 5125,
			],
			'revslider-prevnext-posts-addon'  => [],
			'revslider-refresh-addon'         => [
				$_js . 'revolution.addon.refresh.min.js' => 920,
			],
			'revslider-rel-posts-addon'       => [],
			'revslider-revealer-addon'        => [
				$_css . 'revolution.addon.revealer.css'            => 792,
				$_css . 'revolution.addon.revealer.preloaders.css' => 14792,
				$_js . 'revolution.addon.revealer.min.js'          => 7533,
			],
			'revslider-sharing-addon'         => [
				$_js . 'revslider-sharing-addon-public.js' => 6232,
			],
			'revslider-slicey-addon'          => [
				$_js . 'revolution.addon.slicey.min.js' => 4772,
			],
			'revslider-snow-addon'            => [
				$_js . 'revolution.addon.snow.min.js' => 4823,
			],
			'revslider-template-addon'        => [],
			'revslider-typewriter-addon'      => [
				$_css . 'typewriter.css'                    => 233,
				$_js . 'revolution.addon.typewriter.min.js' => 8038,
			],
			'revslider-weather-addon'         => [
				$_css . 'revslider-weather-addon-icon.css'   => 3699,
				$_css . 'revslider-weather-addon-public.css' => 483,
				$_css . 'weather-icons.css'                  => 31082,
				$_js . 'revslider-weather-addon-public.js'   => 5335,
			],
			'revslider-whiteboard-addon'      => [
				$_js . 'revolution.addon.whiteboard.min.js' => 10649,
			],
		];

		//AddOns can apply/modify the default data here
		$_a = RevLoader::apply_filters('revslider_create_slider_page', $_a, $_css, $_js, $this);

		foreach ($addons as $addon) {

			if (!isset($_a[$addon])) {
				continue;
			}

			$sizes[$addon] = 0;

			if (!empty($_a[$addon])) {

				foreach ($_a[$addon] as $size) {
					$sizes[$addon] += $size;
				}

			}

			//$sizes[$addon] = $_a[$addon];
		}

		return $sizes;
	}

	/**
	 * returns a list of found compressions
	 * @since. 6.1.4
	 **/
	public function compression_settings() {

		$match = [];
		$com = ['gzip', 'compress', 'deflate', 'br']; //'identity' -> means no compression prefered
		$enc = $this->get_val($_SERVER, 'HTTP_ACCEPT_ENCODING');

		if (empty($enc)) {
			return $match;
		}

		foreach ($com as $c) {

			if (strpos($enc, $c) !== false) {
				$match[] = $c;
			}

		}

		return $match;
	}

	/**
	 * get all available languages from Slider Revolution
	 **/
	public function get_available_languages() {

		//track
		return RS_PLUGIN_PATH . 'languages/';

		$lang_codes = [
			'de_DE' => $this->l('German'),
			'en_US' => $this->l('English'),
			'fr_FR' => $this->l('French'),
			'zh_CN' => $this->l('Chinese'),
		];

		$lang = get_available_languages(RS_PLUGIN_PATH . 'languages/');
		$_lang = [];

		if (!empty($lang)) {

			foreach ($lang as $k => $v) {

				if (strpos($v, 'revsliderhelp-') !== false) {
					continue;
				}

				$_lc = str_replace('revslider-', '', $v);
				$_lang[$_lc] = (isset($lang_codes[$_lc])) ? $lang_codes[$_lc] : $_lc;
			}

		}

		return $_lang;
	}

}
