<?php


class ComposerBase {

	
	protected $shortcode_edit_form = false;
	
	protected $templates_editor = false;
	
	protected $shortcodes = [];
    
    
	public function init() {

		$this->addPageCustomCss();

	}
    
	public function allvcshortcodeobj() {

		return $this->shortcodes;
	}

	public function initAdmin() {

        $this->setEditForm(new ComposerShortcodeEditForm());
        $this->setTemplatesEditor(new ComposerTemplatesEditor());
		$this->editForm()->init();
		$this->templatesEditor()->init();
        $this->addPageCustomCss();
	}

	public function setEditForm(ComposerShortcodeEditForm $form) {

		$this->shortcode_edit_form = $form;
	}
    
    public function setTemplatesEditor(ComposerTemplatesEditor $editor) {

		$this->templates_editor = $editor;
	}


	public function editForm() {

		return $this->shortcode_edit_form;
	}
    
    public function templatesEditor() {

		return $this->templates_editor;
	}	

	public function jsForceSend($args) {

		$args['send'] = true;
		return $args;
	}
	
	public function addMetaData() {

		echo '<meta name="generator" content="Powered by Visual Composer - drag and drop page builder for EphenyxShop."/>' . "\n";
	}

	public function addShortCode($shortcode) {
        
        $this->shortcodes[$shortcode['base']] = new ComposerShortCodeFishBones($shortcode);
        
	}

	public function getShortCode($tag) {
       
		return $this->shortcodes[$tag];
	}

	public function removeShortCode($tag) {
        
        Composer::remove_shortcode($tag);
	}

	
	public static function galleryHTML() {

		$images = Tools::getValue('content');

		if (!empty($images)) {
			echo fieldAttachedImages(explode(",", $images));
		}

		die();
	}

	/**
	 * Rewrite code or name
	 */
	public function createShortCodes() {

		remove_all_shortcodes();

		foreach (ComposerMap::getShortCodes() as $sc_base => $el) {
			$this->shortcodes[$sc_base] = new ComposerShortCodeFishBones($el);
		}

	}

	public function updateShortcodeSetting($tag, $name, $value) {

		$this->shortcodes[$tag]->setSettings($name, $value);
	}

	public function buildShortcodesCustomCss($post_id) {

		$post = get_post($post_id);
        
		$css = $this->parseShortcodesCustomCss($post->post_content);

		if (empty($css)) {
			delete_post_meta($post_id, '_wpb_shortcodes_custom_css'); // cms_vc_custom_css_2
		} else {
			update_post_meta($post_id, '_wpb_shortcodes_custom_css', $css);
		}

	}

	protected function parseShortcodesCustomCss($content) {

		$css = '';

		if (!preg_match('/\s*(\.[^\{]+)\s*\{\s*([^\}]+)\s*\}\s*/', $content)) {
			return $css;
		}

		preg_match_all('/' . get_shortcode_regex() . '/', $content, $shortcodes);

		foreach ($shortcodes[2] as $index => $tag) {
			$shortcode = ComposerMap::getShortCode($tag);
			$attr_array = shortcode_parse_atts(trim($shortcodes[3][$index]));

			foreach ($shortcode['params'] as $param) {

				if ($param['type'] == 'css_editor' && isset($attr_array[$param['param_name']])) {
					$css .= $attr_array[$param['param_name']];
				}

			}

		}

		foreach ($shortcodes[5] as $shortcode_content) {
			$css .= $this->parseShortcodesCustomCss($shortcode_content);
		}

		return $css;
	}

	public function addPageCustomCss($custom_page_id = null, $custom_page_type = null) {

		$context = Context::getContext();
		$id_lang = $context->language->id;

		if (empty($custom_page_id)) {

			if (Tools::getValue('controller') == 'adminsuppliers') {
				$page_type = 'sup';
				$page_id = Tools::getValue('id_supplier') ? Tools::getValue('id_supplier') : "null";
			} else if (Tools::getValue('controller') == 'adminmanufacturers') {
				$page_type = 'man';
				$page_id = Tools::getValue('id_manufacturer') ? Tools::getValue('id_manufacturer') : "null";
			} else if (Tools::getValue('controller') == 'AdminCategories') {
				$page_type = 'cat';
				$page_id = Tools::getValue('id_category') ? Tools::getValue('id_category') : "null";
			} else if (Tools::getValue('controller') == 'admincms') {
				$page_type = 'cms';
				$page_id = Tools::getValue('id_cms') ? Tools::getValue('id_cms') : "null";
			} else if (Tools::getValue('controller') == 'adminpagecms') {
				$page_type = 'page_cms';
				$page_id = Tools::getValue('id_page_cms') ? Tools::getValue('id_page_cms') : "null";
			} else if (Tools::getValue('controller') == 'admincontentanywhere') {
				$page_type = 'vccaw';
				$page_id = Tools::getValue('id_contentanywhere') ? Tools::getValue('id_contentanywhere') : "null";
			} else if (Tools::getValue('controller') == 'adminvcproducttabcreator') {
				$page_type = 'vctc';
				$page_id = Tools::getValue('id_vcproducttabcreator') ? Tools::getValue('id_vcproducttabcreator') : "null";
			} else if (Tools::getValue('controller') == 'VC_frontend') {

				if (Tools::getValue('id_cms')) {
					$page_type = 'cms';
					$page_id = Tools::getValue('id_cms') ? Tools::getValue('id_cms') : "null";
				} else if (Tools::getValue('id_category')) {
					$page_id = Tools::getValue('id_category');
					$page_type = 'cat';
				}

			} else {
				$page_type = isset($context->controller->php_self) ? $context->controller->php_self : null;
				$page_id = null;

				if ($page_type == 'product' && Tools::getValue('id_product')) {
					$page_id = (int)(Tools::getValue('id_product'));
				} else if ($page_type == 'category' && Tools::getValue('id_category')) {
					$page_id = (int)(Tools::getValue('id_category'));
					$page_type = 'cat';
				} else if ($page_type == 'cms' && isset($context->controller->$page_type->id)) {
					$page_id = $context->controller->$page_type->id;
				} else if (Tools::getValue('controller') == 'details' && Tools::getValue('id_post')) {
					// smartblog
					$page_id = Tools::getValue('id_post');
					$page_type = 'smartblog';
				} else if (Tools::getValue('controller') == 'supplier' && Tools::getValue('id_supplier')) {
					$page_id = Tools::getValue('id_supplier');
					$page_type = 'sup';
				} else if (Tools::getValue('controller') == 'manufacturer' && Tools::getValue('id_manufacturer')) {
					$page_id = Tools::getValue('id_manufacturer');
					$page_type = 'man';
				}

			}

		} else {
			$page_id = $custom_page_id;
			$page_type = $custom_page_type;
		}

		if (!empty($page_id)) {
			$id = $page_id;
			$optionname = "_wpb_{$page_type}_{$id}_{$id_lang}_css";

			$post_custom_css = Configuration::get($optionname);

			if (!empty($post_custom_css)) {
				echo '<style type="text/css" data-type="vc_custom-css">';
				echo htmlspecialchars_decode($post_custom_css);
				echo '</style>';
			}

		}

	}

}
