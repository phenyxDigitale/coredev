<?php

/**
 * Renders navigation bar for Editors.
 */
class ComposerNavbar {

	protected $controls = [
		'add_element',
		'templates',
		'custom_css',
	];
	protected $brand_url = 'http://ephenyx.shop';
	protected $css_class = 'vc_navbar';
	protected $controls_filter_name = 'vc_nav_controls';
	protected $post = false;
    public $context;

	public function __construct($post = '') {

		$this->post = $post;
        global $smarty;
        $this->context = Context::getContext();
	}

	/**
	 * Generate array of controls by iterating property $controls list.
	 *
	 * @return array - list of arrays witch contains key name and html output for button.
	 */
	public function getControls() {

		$list = [];
		$composer = Composer::getInstance();


		foreach ($this->controls as $control) {
			$method = $composer->vc_camel_case('get_control_' . $control);

			if (method_exists($this, $method)) {
				$list[] = [$control, $this->$method() . "\n"];
			}

		}

		return $list;
	}

	/**
	 * Get current post.
	 * @return null|WP_Post
	 */
	public function post() {

		$id = Tools::getValue('id_cms');

		if ($this->post) {
			return $this->post;
		}

		return new CMS($id);
	}

	/**
	 * Render template.
	 */
	public function render() {
        
        $data = $this->context->smarty->createTemplate(_EPH_COMPOSER_DIR_ .  'editors/navbar/navbar.tpl');
        $data->assign(
				[
					'css_class' => $this->css_class,
			        'controls'  => $this->getControls(),
			        'nav_bar'   => $this,
			         'post'      => '',
				]
			);
        
        return $data->fetch();

		
	}

	public function getLogo() {
        $composer = Composer::getInstance();
		$output = '<a id="vc_logo" class="vc_navbar-brand" title="' . $composer->esc_attr('Visual Composer')
		. '" href="' . $composer->esc_attr($this->brand_url) . '" target="_blank">'
		. $composer->l('Visual Composer') . '</a>';
		return $output;
	}

	public function getControlCustomCss() {
        $composer = Composer::getInstance();
		return '<li class="vc_pull-right"><a id="vc_post-settings-button" class="vc_icon-btn vc_post-settings" title="'
		. $composer->esc_attr('Page settings') . '">'
		. '<span id="vc_post-css-badge" class="vc_badge vc_badge-custom-css" style="display: none;">' . $composer->l('CSS') . '</span></a>'
			. '</li>';
	}

	public function getControlAddElement() {
        $composer = Composer::getInstance();
		return '<li class="vc_show-mobile">'
		. '	<a href="javascript:;" class="vc_icon-btn vc_element-button" data-model-id="vc_element" id="vc_add-new-element" title="'
		. '' . $composer->l('Add new element') . '">'
			. '	</a>'
			. '</li>';
	}

	public function getControlTemplates() {
        $composer = Composer::getInstance();
		return '<li><a href="javascript:;" class="vc_icon-btn vc_templates-button vc_navbar-border-right"  id="vc_templates-editor-button" title="'
		. $composer->l('Templates') . '"></a></li>';
	}

	

}
