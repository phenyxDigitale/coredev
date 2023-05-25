<?php

class ComposerShortCode_vc_posts_grid extends ComposerShortCode {

	protected $filter_categories = [];
	protected $query = false;
	protected $loop_args = [];
	protected $taxonomies = false;
	protected $partial_paths = [];
	protected static $pretty_photo_loaded = false;
	protected $teaser_data = false;
	protected $block_template_dir_name = 'post_block';
	protected $block_template_filename = '_item.php';
	protected static $meta_data_name = 'vc_teaser';

	function __construct($settings) {

		parent::__construct($settings);
	}

	public function jsComposerEditPage() {

		$pt_array = vc_editor_post_types();

		foreach ($pt_array as $pt) {
			add_meta_box('vc_teaser', __('VC: Custom Teaser', "js_composer"), [&$this, 'outputTeaser'], $pt, 'side');
		}

		add_action('save_post', [&$this, 'saveTeaserMetaBox']);
	}

	/**
	 * Get teaser box data from database.
	 *
	 * @param $name
	 * @param bool $id
	 * @return string
	 */
	public function getTeaserData($name, $id = false) {

		if ($id === false) {
			$id = get_the_ID();
		}

		$this->teaser_data = get_post_meta($id, self::$meta_data_name, true);
		return isset($this->teaser_data[$name]) ? $this->teaser_data[$name] : '';
	}

	protected function getCategoriesCss($post_id) {

		$categories_css = '';
		$db = Db::getInstance();

		$post_categories = $db->executeS('SELECT id_category FROM ' . _DB_PREFIX_ . "smart_blog_post WHERE id_smart_blog_post={$post_id}", true, false);

		if (!empty($post_categories)) {
			foreach ($post_categories as $cat) {
				$cat = (int)($cat['id_category']);

				if (!in_array($cat, $this->filter_categories)) {
					$this->filter_categories[] = $cat;
				}

				$categories_css .= ' grid-cat-' . $cat;
			}
		}

		return $categories_css;
	}

	protected function resetTaxonomies() {

		$this->taxonomies = false;
	}

	protected function getTaxonomies() {

		if ($this->taxonomies === false) {
			$this->taxonomies = get_object_taxonomies(!empty($this->loop_args['post_type']) ? $this->loop_args['post_type'] : get_post_types(['public' => false, 'name' => 'attachment'], 'names', 'NOT'));
		}

		return $this->taxonomies;
	}

	protected function getLoop($loop) {

		require_once DIGITAL_CORE_DIR. '/vendor/loop/loop.php';
		list($this->loop_args, $this->query) = build_loop_query($loop);
	}

	protected function spanClass($grid_columns_count) {

		$teaser_width = '';

		switch ($grid_columns_count) {
		case '1':
			$teaser_width = 'vc_col-sm-12';
			break;
		case '2':
			$teaser_width = 'vc_col-sm-6';
			break;
		case '3':
			$teaser_width = 'vc_col-sm-4';
			break;
		case '4':
			$teaser_width = 'vc_col-sm-3';
			break;
		case '5':
			$teaser_width = 'vc_col-sm-10';
			break;
		case '6':
			$teaser_width = 'vc_col-sm-2';
			break;
		}

		
		$custom = get_custom_column_class($teaser_width);
		return $custom ? $custom : $teaser_width;
	}

	protected function getMainCssClass($filter) {

		return 'wpb_' . ($filter === 'yes' ? 'filtered_' : '') . 'grid';
	}

	protected function getFilterCategories() {

		return get_terms($this->getTaxonomies(), [
			'orderby' => 'name',
			'include' => implode(',', $this->filter_categories),
		]);
	}

	protected function getPostThumbnail($post_id, $grid_thumb_size = 'full') {

		$nthumbs = Composer::getSmartBlogPostsThumbSizes();

		if (in_array($grid_thumb_size, array_values($nthumbs))) {
			return "{$post_id}-{$grid_thumb_size}.jpg";
		} else {
			return "{$post_id}.jpg";
		}

	}

	protected function getPostContent() {

		remove_filter('the_content', 'wpautop');
		$content = str_replace(']]>', ']]&gt;', apply_filters('the_content', get_the_content()));
		return $content;
	}

	protected function getPostExcerpt() {

		remove_filter('the_excerpt', 'wpautop');
		$content = apply_filters('the_excerpt', get_the_excerpt());
		return $content;
	}

	protected function getLinked($post, $content, $type, $css_class) {

		$output = '';
		$vc_manager = ephenyx_manager();

		if ($type === 'link_post' || empty($type)) {
//			$url = get_permalink( $post->id );
			$url = $post->link;
			$title = sprintf($vc_manager->esc_attr__('Permalink to %s'), $post->title_attribute);
			$output .= '<a href="' . $url . '" class="' . $css_class . '"' . $this->link_target . ' title="' . $title . '">' . $content . '</a>';
		} else if ($type === 'link_image' && isset($post->image_link) && !empty($post->image_link)) {
			$this->loadPrettyPhoto();
			$output .= '<a href="' . $post->image_link . '" class="' . $css_class . ' prettyphoto"' . $this->link_target . ' title="' . $post->title_attribute . '">' . $content . '</a>';
		} else {
			$output .= $content;
		}

		return $output;
	}

	protected function loadPrettyPhoto() {

		if (true !== self::$pretty_photo_loaded) {
			Context::getContext()->controller->addJS(vc_asset_url('lib/prettyphoto/js/jquery.prettyPhoto.js'));
			Context::getContext()->controller->addCSS(vc_asset_url('lib/prettyphoto/css/prettyPhoto.css'));
			self::$pretty_photo_loaded = true;
		}

	}

	protected function setLinkTarget($grid_link_target = '') {

		$this->link_target = $grid_link_target == '_blank' ? ' target="_blank"' : '';
	}

	protected function findBlockTemplate() {

		$template_path = $this->block_template_dir_name . '/' . $this->block_template_filename;
		// Check template path in shortcode's mapping settings

		if (!empty($this->settings['html_template']) && is_file($this->settings('html_template') . $template_path)) {
			return $this->settings['html_template'] . $template_path;
		}

		// Check template in theme directory
		$user_template = vc_shortcodes_theme_templates_dir($template_path);

		if (is_file($user_template)) {
			return $user_template;
		}

		// Check default place
		$default_dir = ephenyx_manager()->getDefaultShortcodesTemplatesDir() . '/';

		if (is_file($default_dir . $template_path)) {
			return $default_dir . $template_path;
		}

		return $template_path;
	}

	protected function getBlockTemplate() {

		if (!isset($this->block_template_path)) {
			$this->block_template_path = $this->findBlockTemplate();
		}

		return $this->block_template_path;
	}

}
