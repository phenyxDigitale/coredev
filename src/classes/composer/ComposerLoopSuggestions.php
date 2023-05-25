<?php

class ComposerLoopSuggestions {

//	protected $content = array();
	protected $content = '';
	protected $exclude = [];
	protected $field;

	function __construct($field, $query, $exclude) {

		$this->exclude = explode(',', $exclude);
		$method_name = 'get_' . preg_replace('/_out$/', '', $field);

		if (method_exists($this, $method_name)) {
			call_user_func([$this, $method_name], $query);
		}

	}

	public function get_authors($query) {

		$args = !empty($query) ? ['search' => '*' . $query . '*', 'search_columns' => ['user_nicename']] : [];

		if (!empty($this->exclude)) {
			$args['exclude'] = $this->exclude;
		}

		$users = get_users($args);

		foreach ($users as $user) {
			$this->content[] = ['value' => (string) $user->ID, 'name' => (string) $user->data->user_nicename];
		}

	}

	public function get_categories($query) {

//		$args = ! empty( $query ) ? array( 'search' => $query ) : array();

		$exclude = $exid = '';

		if (!empty($this->exclude)) {

			foreach ($this->exclude as $k => $v) {

				if (empty($v)) {
					continue;
				}

				if ($k > 0 && !empty($exid)) {
					$exid .= ',';
				}

				$exid .= $v;
			}

			if (!empty($exid)) {
				$exclude = 'sbc.id_smart_blog_category NOT IN(';
				$exclude .= $exid;
				$exclude .= ') AND ';
			}

		}

		$limit = vc_post_param('limit') ? vc_post_param('limit') : 20;
		$id_lang = (int) Context::getContext()->language->id;

		$titlefield = 'meta_title';
		$smartblog = Plugin::getInstanceByName('smartblog');

		if (Tools::version_compare($smartblog->version, '2.1', '>=')) {
			$titlefield = 'name';
		}

		$categories = Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS('
        SELECT * FROM `' . _DB_PREFIX_ . 'smart_blog_category` sbc INNER JOIN `' . _DB_PREFIX_ . 'smart_blog_category_lang` sbcl ON(sbc.`id_smart_blog_category` = sbcl.`id_smart_blog_category` AND sbcl.`id_lang` = ' . (int) ($id_lang) . ')
INNER JOIN `' . _DB_PREFIX_ . 'smart_blog_category_shop` sbs ON sbs.id_smart_blog_category = sbc.id_smart_blog_category WHERE ' . $exclude . 'sbc.`active`= 1 AND sbs.id_shop = ' . (int) Context::getContext()->company->id . ' AND sbcl.' . $titlefield . ' LIKE "%' . $query . '%" LIMIT ' . $limit);

		if (empty($categories)) {$this->content = '';return;}

		foreach ($categories as $cat) {
			$this->content .= $cat[$titlefield] . '|';
			$this->content .= $cat['id_smart_blog_category'] . "\n";
		}

	}

	public function get_tags($query) {

		$smartblog = Plugin::getInstanceByName('smartblog');

		if (Tools::version_compare($smartblog->version, '2.1', '>=')) {
			$id_lang = (int) Context::getContext()->language->id;
			$limit = vc_post_param('limit') ? vc_post_param('limit') : 20;
			$exclude = $exid = '';

			if (!empty($this->exclude)) {

				foreach ($this->exclude as $k => $v) {

					if (empty($v)) {
						continue;
					}

					if ($k > 0 && !empty($exid)) {
						$exid .= ',';
					}

					$exid .= $v;
				}

				if (!empty($exid)) {
					$exclude = 'id_tag NOT IN(';
					$exclude .= $exid;
					$exclude .= ') AND ';
				}

			}

			$sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'smart_blog_tag` WHERE ' . $exclude . ' id_lang=' . $id_lang . ' AND name LIKE "%' . $query . '%" LIMIT ' . $limit;
			$categories = Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS($sql);

			if (empty($categories)) {$this->content = '';return;}

			foreach ($categories as $cat) {
				$this->content .= $cat['name'] . '|';
				$this->content .= $cat['id_tag'] . "\n";
			}

		}

	}

	public function get_tax_query($query) {

		$args = !empty($query) ? ['search' => $query] : [];

		if (!empty($this->exclude)) {
			$args['exclude'] = $this->exclude;
		}

		$tags = get_terms(VcLoopSettings::getTaxonomies(), $args);

		foreach ($tags as $tag) {
			$this->content[] = ['value' => $tag->term_id, 'name' => $tag->name];
		}

	}

	public function get_by_id($query) {

		$args = !empty($query) ? ['s' => $query, 'post_type' => 'any'] : ['post_type' => 'any'];

		if (!empty($this->exclude)) {
			$args['exclude'] = $this->exclude;
		}

		$posts = get_posts($args);

		foreach ($posts as $post) {
			$this->content[] = ['value' => $post->ID, 'name' => $post->post_title];
		}

	}

	public function render() {

		echo $this->content;
	}

}
