<?php

class ComposerLoopSettings extends Composer {

	// Available parts of loop for WP_Query object.
	protected $content = [];
	protected $parts;
	protected $query_parts = [
		'size',
		'order_by',
		'order',
		'categories',
		'tags',
	];

	function __construct($value, $settings = []) {

		$vc_manager = ephenyx_manager();
		$this->parts = [
			'size'       => $vc_manager->l('Post Count'),
			'order_by'   => $vc_manager->l('Order By'),
			'order'      => $vc_manager->l('Order'),
			'categories' => $vc_manager->l('Categories'),
			'tags'       => $vc_manager->l('Tags'),
		];
		$this->settings = $settings;

		// Parse loop string
		$data = $this->parseData($value);

		foreach ($this->query_parts as $part) {
			$value = isset($data[$part]) ? $data[$part] : '';
			$locked = $this->getSettings($part, 'locked') === 'true';
			// Predefined value check.

			if (!is_null($this->getSettings($part, 'value')) && $this->replaceLockedValue($part)
				&& ($locked === true || strlen((string) $value) == 0)
			) {
				$value = $this->settings[$part]['value'];
			} else if (!is_null($this->getSettings($part, 'value')) && !$this->replaceLockedValue($part)
				&& ($locked === true || strlen((string) $value) == 0)
			) {
				$value = implode(',', array_unique(explode(',', $value . ',' . $this->settings[$part]['value'])));
			}

			// Find custom method for parsing

			if (method_exists($this, 'parse_' . $part)) {
				$method = 'parse_' . $part;
				//$this->content[$part] = $this->$method( $value );
				$this->content[$part] = call_user_func([$this, $method], $value);
			} else {
				$this->content[$part] = $this->parseString($value);
			}

			// Set locked if value is locked by settings

			if ($locked) {
				$this->content[$part]['locked'] = true;
			}

			//

			if ($this->getSettings($part, 'hidden') === 'true') {
				$this->content[$part]['hidden'] = true;
			}

		}

	}

	protected function replaceLockedValue($part) {

		return in_array($part, ['size', 'order_by', 'order']);
	}

	public function getLabel($key) {

		return isset($this->parts[$key]) ? $this->parts[$key] : $key;
	}

	public function getSettings($part, $name) {

		$settings_exists = isset($this->settings[$part]) && is_array($this->settings[$part]);
		return $settings_exists && isset($this->settings[$part][$name]) ? $this->settings[$part][$name] : null;
	}

	public function parseString($value) {

		return ['value' => $value];
	}

	protected function parseDropDown($value, $options = []) {

		return ['value' => $value, 'options' => $options];
	}

	protected function parseMultiSelect($value, $options = []) {

		return ['value' => explode(',', $value), 'options' => $options];
	}

	public function parse_order_by($value) {

		$vc_manager = ephenyx_manager();
		return $this->parseDropDown($value, [
			//array('none', $vc_manager->l("None")),
			['created', $vc_manager->l("Date")],
			['id_smart_blog_post', $vc_manager->l("ID")],
//			array( 'author', $vc_manager->l("Author") ),
			['meta_title', $vc_manager->l("Title")],
			//'name',
			['modified', $vc_manager->l("Modified")],
			//'parent',
			['position', $vc_manager->l("Position")],
//			array( 'comment_count', $vc_manager->l("Comment count") ),
			['link_rewrite', $vc_manager->l("Slug")],

		]);
	}

	public function parse_order($value) {

		$vc_manager = ephenyx_manager();
		return $this->parseDropDown($value, [
			['ASC', $vc_manager->l("Ascending")],
			['DESC', $vc_manager->l("Descending")],
		]);
	}

	public function parse_post_type($value) {

		$options = [];
		$args = [
			'public' => true,
		];
		$post_types = get_post_types($args);

		foreach ($post_types as $post_type) {

			if ($post_type != 'attachment') {
				$options[] = $post_type;
			}

		}

		return $this->parseMultiSelect($value, $options);
	}

	public function parse_authors($value) {

		$options = $not_in = [];

		if (empty($value)) {
			return $this->parseMultiSelect($value, $options);
		}

		$list = explode(',', $value);

		foreach ($list as $id) {

			if ((int) $id < 0) {
				$not_in[] = abs($id);
			}

		}

		$users = get_users(['include' => array_map('abs', $list)]);

		foreach ($users as $user) {
			$options[] = [
				'value'  => (string) $user->ID,
				'name'   => $user->data->user_nicename,
				'action' => in_array((int) $user->ID, $not_in) ? '-' : '+',
			];
		}

		return $this->parseMultiSelect($value, $options);
	}

	public function parse_categories($value) {

		$options = $not_in = [];

		if (empty($value)) {
			return $this->parseMultiSelect($value, $options);
		}

		$list = explode(',', $value);

		foreach ($list as $id) {

			if ((int) $id < 0) {
				$not_in[] = abs($id);
			}

		}

		$id_lang = (int) Context::getContext()->language->id;

		$titlefield = 'meta_title';
		$smartblog = Plugin::getInstanceByName('smartblog');

		if (Tools::version_compare($smartblog->version, '2.1', '>=')) {
			$titlefield = 'name';
		}

		$list = Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'smart_blog_category` sbc INNER JOIN `' . _DB_PREFIX_ . 'smart_blog_category_lang` sbcl ON(sbc.`id_smart_blog_category` = sbcl.`id_smart_blog_category` AND sbcl.`id_lang` = ' . (int) ($id_lang) . ')
		INNER JOIN `' . _DB_PREFIX_ . 'smart_blog_category_shop` sbs ON sbs.id_smart_blog_category = sbc.id_smart_blog_category WHERE sbc.id_smart_blog_category IN(' . $value . ') AND sbc.`active`= 1 AND sbs.id_shop = ' . (int) Context::getContext()->company->id);

//                $list = get_categories( array( 'include' => array_map( 'abs', $list ) ) );

		foreach ($list as $obj) {
			$options[] = [
				'value'  => (string) $obj['id_smart_blog_category'],
				'name'   => $obj[$titlefield],
				'action' => in_array((int) $obj['id_smart_blog_category'], $not_in) ? '-' : '+',
			];
		}

		return $this->parseMultiSelect($value, $options);

	}

	public function parse_tags($value) {

		$options = $not_in = [];

		if (empty($value)) {
			return $this->parseMultiSelect($value, $options);
		}

		$list = explode(',', $value);

		foreach ($list as $id) {

			if ((int) $id < 0) {
				$not_in[] = abs($id);
			}

		}

		$id_lang = (int) Context::getContext()->language->id;

		$smartblog = Plugin::getInstanceByName('smartblog');

		if (Tools::version_compare($smartblog->version, '2.1', '>=')) {
			$sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'smart_blog_tag` WHERE id_lang=' . $id_lang . ' AND id_tag IN(' . $value . ')';
			$list = Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS($sql);

			foreach ($list as $obj) {
				$options[] = [
					'value'  => (string) $obj['id_tag'],
					'name'   => $obj['name'],
					'action' => in_array((int) $obj['id_tag'], $not_in) ? '-' : '+',
				];
			}

			return $this->parseMultiSelect($value, $options);
		}

	}

	public function parse_tax_query($value) {

		$options = $not_in = [];

		if (empty($value)) {
			return $this->parseMultiSelect($value, $options);
		}

		$list = explode(',', $value);

		foreach ($list as $id) {

			if ((int) $id < 0) {
				$not_in[] = abs($id);
			}

		}

		$list = get_terms(VcLoopSettings::getTaxonomies(), ['include' => array_map('abs', $list)]);

		foreach ($list as $obj) {
			$options[] = [
				'value'  => (string) $obj->term_id,
				'name'   => $obj->name,
				'action' => in_array((int) $obj->term_id, $not_in) ? '-' : '+',
			];
		}

		return $this->parseMultiSelect($value, $options);
	}

	public function parse_by_id($value) {

		$options = $not_in = [];

		if (empty($value)) {
			return $this->parseMultiSelect($value, $options);
		}

		$list = explode(',', $value);

		foreach ($list as $id) {

			if ((int) $id < 0) {
				$not_in[] = abs($id);
			}

		}

		$list = get_posts(['post_type' => 'any', 'include' => array_map('abs', $list)]);

		foreach ($list as $obj) {
			$options[] = [
				'value'  => (string) $obj->ID,
				'name'   => $obj->post_title,
				'action' => in_array((int) $obj->ID, $not_in) ? '-' : '+',
			];
		}

		return $this->parseMultiSelect($value, $options);
	}

	public function render() {

		echo json_encode($this->content);
	}

	public function getContent() {

		return $this->content;
	}

	/**
	 * get list of taxonomies which has no tags and categories items.
	 *
	 * @static
	 * @return array
	 */
	public static function getTaxonomies() {

		$taxonomy_exclude = (array) apply_filters('get_categories_taxonomy', 'category');
		$taxonomy_exclude[] = 'post_tag';
		$taxonomies = [];

		foreach (get_taxonomies() as $taxonomy) {

			if (!in_array($taxonomy, $taxonomy_exclude)) {
				$taxonomies[] = $taxonomy;
			}

		}

		return $taxonomies;
	}

	public static function buildDefault($settings) {

		if (!isset($settings['settings']) || !is_array($settings['settings'])) {
			return '';
		}

		$value = '';

		foreach ($settings['settings'] as $key => $val) {

			if (isset($val['value'])) {
				$value .= (empty($value) ? '' : '|') . $key . ':' . $val['value'];
			}

		}

		return $value;
	}

	public static function buildWpQuery($query, $exclude_id = false) {

		$data = self::parseData($query);
		//  var_dump($data);die();
		$query_builder = new VcLoopQueryBuilder($data);

		if ($exclude_id) {
			$query_builder->excludeId($exclude_id);
		}

		return $query_builder->build($exclude_id);
	}

	public static function parseData($value) {

		$data = [];
		$values_pairs = preg_split('/\|/', $value);

		foreach ($values_pairs as $pair) {

			if (!empty($pair)) {
				list($key, $value) = preg_split('/\:/', $pair);
				$data[$key] = $value;
			}

		}

		return $data;
	}

}
