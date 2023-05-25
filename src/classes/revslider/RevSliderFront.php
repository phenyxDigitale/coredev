<?php

$revslider_rev_start_size_loaded = false;

class RevSliderFront extends RevSliderFunction {

	const TABLE_OPTIONS_NAME = 'revslider_options';
	const TABLE_SLIDER = 'revslider_slider';
	const TABLE_SLIDES = 'revslider_slide';
	const TABLE_STATIC_SLIDES = 'revslider_static_slide';
	const TABLE_CSS = 'revslider_css';
	const TABLE_LAYER_ANIMATIONS = 'revslider_layer_animations';
	const TABLE_NAVIGATIONS = 'revslider_navigations';
	const CURRENT_TABLE_VERSION = '1.0.8';

	const YOUTUBE_ARGUMENTS = 'hd=1&amp;wmode=opaque&amp;showinfo=0&amp;rel=0';
	const VIMEO_ARGUMENTS = 'title=0&amp;byline=0&amp;portrait=0&amp;api=1';

	public function __construct() {

		RevLoader::add_action('wp_enqueue_scripts', ['RevSliderFront', 'add_actions']);
	}

	public static function add_actions() {

		global $wp_version, $revslider_is_preview_mode;

		$func = new RevSliderFunction();
		$css = new RevSliderCssParser();
		$rs_ver = RevLoader::apply_filters('revslider_remove_version', RS_REVISION);
		$global = $func->get_global_settings();
		$inc_global = $func->_truefalse($func->get_val($global, 'allinclude', true));

		$inc_footer = $func->_truefalse($func->get_val($global, ['script', 'footer'], false));
		$waitfor = ['jquery'];
		//$widget	 = RevLoader::is_active_widget(false, false, 'rev-slider-widget', true);

		$load = false;
		$load = RevLoader::apply_filters('revslider_include_libraries', $load);
		$load = ($revslider_is_preview_mode === true) ? true : $load;
		$load = ($inc_global === true) ? true : $load;
		//$load = (self::has_shortcode('rev_slider') === true) ? true : $load;
		//$load = ($widget !== false) ? true : $load;

		if ($inc_global === false) {
			$output = new RevSliderOutput();
			$output->set_add_to($func->get_val($global, 'includeids', ''));
			$add_to = $output->check_add_to(true);
			$load = ($add_to === true) ? true : $load;
		}

		if ($load === false) {
			return false;
		}

		if (Tools::getValue('controller') == 'AdminRevolutionsliderAjax') {
			RevLoader::wp_enqueue_style('rs-plugin-settings', $rs_ver, [], RS_PLUGIN_URL . 'public/assets/css/rs6.css');
		}

		/**
		 * Fix for WordPress versions below 3.7
		 **/
		$style_pre = ($wp_version < 3.7) ? '<style type="text/css">' : '';
		$style_post = ($wp_version < 3.7) ? '</style>' : '';
		$custom_css = $func->get_static_css();
		$custom_css = $css->compress_css($custom_css);
		$custom_css = (trim($custom_css) == '') ? '#rs-demo-id {}' : $custom_css;

		global $wp_scripts;

		if (version_compare($func->get_val($wp_scripts, ['registered', 'tp-tools', 'ver'], '1.0'), RS_TP_TOOLS, '<')) {
			//wp_deregister_script('tp-tools');
			//wp_dequeue_script('tp-tools');
		}

		if (Tools::getValue('controller') == 'AdminRevolutionsliderAjax') {
			RevLoader::wp_enqueue_script('tp-tools', RS_PLUGIN_URL . 'public/assets/js/rbtools.min.js', $waitfor, RS_TP_TOOLS, $inc_footer);

			if (!file_exists(RS_PLUGIN_PATH . 'public/assets/js/rs6.min.js')) {
				RevLoader::wp_enqueue_script('revmin', RS_PLUGIN_URL . 'public/assets/js/dev/rs6.main.js', 'tp-tools', $rs_ver, $inc_footer);
				//if on, load all libraries instead of dynamically loading them
				RevLoader::wp_enqueue_script('revmin-actions', RS_PLUGIN_URL . 'public/assets/js/dev/rs6.actions.js', 'tp-tools', $rs_ver, $inc_footer);
				RevLoader::wp_enqueue_script('revmin-carousel', RS_PLUGIN_URL . 'public/assets/js/dev/rs6.carousel.js', 'tp-tools', $rs_ver, $inc_footer);
				RevLoader::wp_enqueue_script('revmin-layeranimation', RS_PLUGIN_URL . 'public/assets/js/dev/rs6.layeranimation.js', 'tp-tools', $rs_ver, $inc_footer);
				RevLoader::wp_enqueue_script('revmin-navigation', RS_PLUGIN_URL . 'public/assets/js/dev/rs6.navigation.js', 'tp-tools', $rs_ver, $inc_footer);
				RevLoader::wp_enqueue_script('revmin-panzoom', RS_PLUGIN_URL . 'public/assets/js/dev/rs6.panzoom.js', 'tp-tools', $rs_ver, $inc_footer);
				RevLoader::wp_enqueue_script('revmin-parallax', RS_PLUGIN_URL . 'public/assets/js/dev/rs6.parallax.js', 'tp-tools', $rs_ver, $inc_footer);
				RevLoader::wp_enqueue_script('revmin-slideanims', RS_PLUGIN_URL . 'public/assets/js/dev/rs6.slideanims.js', 'tp-tools', $rs_ver, $inc_footer);
				RevLoader::wp_enqueue_script('revmin-video', RS_PLUGIN_URL . 'public/assets/js/dev/rs6.video.js', 'tp-tools', $rs_ver, $inc_footer);
			} else {
				RevLoader::wp_enqueue_script('revmin', RS_PLUGIN_URL . 'public/assets/js/rs6.min.js', 'tp-tools', $rs_ver, $inc_footer);
			}

		}

		//commented
		//RevLoader::add_action('wp_head', array('RevSliderFront', 'add_meta_generator'));
		RevLoader::add_action('wp_head', ['RevSliderFront', 'js_set_start_size'], 99);
		RevLoader::add_action('admin_head', ['RevSliderFront', 'js_set_start_size'], 99);
		RevLoader::add_action('wp_footer', ['RevSliderFront', 'load_icon_fonts']);
		RevLoader::add_action('wp_footer', ['RevSliderFront', 'load_google_fonts']);

		//Async JS Loading

		if ($func->_truefalse($func->get_val($global, ['script', 'defer'], false)) === true) {
			RevLoader::add_filter('clean_url', ['RevSliderFront', 'add_defer_forscript'], 11, 1);
		}

		//RevLoader::add_action('wp_before_admin_bar_render', array('RevSliderFront', 'add_admin_menu_nodes'));
		//RevLoader::add_action('wp_footer', array('RevSliderFront', 'add_admin_bar'), 99);
	}

	/**
	 * Add Meta Generator Tag in FrontEnd
	 * @since: 5.0
	 */
	public static function add_meta_generator() {

		echo RevLoader::apply_filters('revslider_meta_generator', '<meta name="generator" content="Powered by Slider Revolution ' . RS_REVISION . ' - responsive, Mobile-Friendly Slider Plugin for WordPress with comfortable drag and drop interface." />' . "\n");
	}

	/**
	 * Load Used Icon Fonts
	 * @since: 5.0
	 */
	public static function load_icon_fonts() {

		global $fa_var, $fa_icon_var, $pe_7s_var;
		$func = new RevSliderFunction();
		$global = $func->get_global_settings();
		$ignore_fa = $func->_truefalse($func->get_val($global, 'fontawesomedisable', false));

		echo ($ignore_fa === false && ($fa_icon_var == true || $fa_var == true)) ? '<link rel="stylesheet" property="stylesheet" id="rs-icon-set-fa-icon-css" href="' . RS_PLUGIN_URL . 'public/assets/fonts/font-awesome/css/font-awesome.css" type="text/css" media="all" />' . "\n" : '';
		echo ($pe_7s_var) ? '<link rel="stylesheet" property="stylesheet" id="rs-icon-set-pe-7s-css" href="' . RS_PLUGIN_URL . 'public/assets/fonts/pe-icon-7-stroke/css/pe-icon-7-stroke.css" type="text/css" media="all" />' . "\n" : '';
	}

	/**
	 * Load Used Google Fonts
	 * add google fonts of all sliders found on the page
	 * @since: 6.0
	 */
	public static function load_google_fonts() {

		$func = new RevSliderFunction();
		$fonts = $func->print_clean_font_import();

		if (!empty($fonts)) {
			echo $fonts . "\n";
		}

	}

	/**
	 * adds async loading
	 * @since: 5.0
	 */
	public static function add_defer_forscript($url) {

		if (strpos($url, 'rs6.min.js') === false && strpos($url, 'rbtools.min.js') === false) {
			return $url;
		} else
		if (Tools::getValue('controller') == 'AdminRevolutionsliderAjax') {
			return $url;
		} else {
			return $url . "' defer='defer";
		}

	}

	public static function js_set_start_size() {

		global $revslider_rev_start_size_loaded;

		if ($revslider_rev_start_size_loaded === true) {
			return false;
		}

		$script = '<script type="text/javascript">';
		$script .= 'function setREVStartSize(e){
			//window.requestAnimationFrame(function() {
				window.RSIW = window.RSIW===undefined ? window.innerWidth : window.RSIW;
				window.RSIH = window.RSIH===undefined ? window.innerHeight : window.RSIH;
				try {
					var pw = document.getElementById(e.c).parentNode.offsetWidth,
						newh;
					pw = pw===0 || isNaN(pw) ? window.RSIW : pw;
					e.tabw = e.tabw===undefined ? 0 : parseInt(e.tabw);
					e.thumbw = e.thumbw===undefined ? 0 : parseInt(e.thumbw);
					e.tabh = e.tabh===undefined ? 0 : parseInt(e.tabh);
					e.thumbh = e.thumbh===undefined ? 0 : parseInt(e.thumbh);
					e.tabhide = e.tabhide===undefined ? 0 : parseInt(e.tabhide);
					e.thumbhide = e.thumbhide===undefined ? 0 : parseInt(e.thumbhide);
					e.mh = e.mh===undefined || e.mh=="" || e.mh==="auto" ? 0 : parseInt(e.mh,0);
					if(e.layout==="fullscreen" || e.l==="fullscreen")
						newh = Math.max(e.mh,window.RSIH);
					else{
						e.gw = Array.isArray(e.gw) ? e.gw : [e.gw];
						for (var i in e.rl) if (e.gw[i]===undefined || e.gw[i]===0) e.gw[i] = e.gw[i-1];
						e.gh = e.el===undefined || e.el==="" || (Array.isArray(e.el) && e.el.length==0)? e.gh : e.el;
						e.gh = Array.isArray(e.gh) ? e.gh : [e.gh];
						for (var i in e.rl) if (e.gh[i]===undefined || e.gh[i]===0) e.gh[i] = e.gh[i-1];

						var nl = new Array(e.rl.length),
							ix = 0,
							sl;
						e.tabw = e.tabhide>=pw ? 0 : e.tabw;
						e.thumbw = e.thumbhide>=pw ? 0 : e.thumbw;
						e.tabh = e.tabhide>=pw ? 0 : e.tabh;
						e.thumbh = e.thumbhide>=pw ? 0 : e.thumbh;
						for (var i in e.rl) nl[i] = e.rl[i]<window.RSIW ? 0 : e.rl[i];
						sl = nl[0];
						for (var i in nl) if (sl>nl[i] && nl[i]>0) { sl = nl[i]; ix=i;}
						var m = pw>(e.gw[ix]+e.tabw+e.thumbw) ? 1 : (pw-(e.tabw+e.thumbw)) / (e.gw[ix]);
						newh =  (e.gh[ix] * m) + (e.tabh + e.thumbh);
					}
					if(window.rs_init_css===undefined) window.rs_init_css = document.head.appendChild(document.createElement("style"));
					document.getElementById(e.c).height = newh+"px";
					window.rs_init_css.innerHTML += "#"+e.c+"_wrapper { height: "+newh+"px }";
				} catch(e){
					console.log("Failure at Presize of Slider:" + e)
				}
			//});
		  };';
		$script .= '</script>' . "\n";
		echo RevLoader::apply_filters('revslider_add_setREVStartSize', $script);

		$revslider_rev_start_size_loaded = true;
	}

	/**
	 * sets the post saving value to true, so that the output echo will not be done
	 **/
	public static function set_post_saving() {

		global $revslider_save_post;
		$revslider_save_post = true;
	}

}

?>