<?php

class ComposerLoopQueryBuilder {

	protected $args = [];

	public function __construct($data) {

		foreach ($data as $key => $value) {
			$method = 'parse_' . $key;

			if (method_exists($this, $method)) {
				call_user_func([$this, $method], $value);
			}

		}

	}

	// Pages count
	protected function parse_size($value) {

		$this->args['posts_per_page'] = $value === 'All' ? -1 : (int) $value;
	}

	// Sorting field
	protected function parse_order_by($value) {

		$this->args['orderby'] = $value;
	}

	// Sorting order
	protected function parse_order($value) {

		$this->args['order'] = $value;
	}

	// By post types
	protected function parse_post_type($value) {

		$this->args['post_type'] = $this->stringToArray($value);
	}

	// By author
	protected function parse_authors($value) {

		$this->args['author'] = $value;
	}

	// By categories
	protected function parse_categories($value) {

		$this->args['cats'] = $value;

	}

	protected function parse_tax_query($value) {

		$terms = $this->stringToArray($value);

		if (empty($this->args['tax_query'])) {
			$this->args['tax_query'] = ['relation' => 'OR'];
		}

		$negative_term_list = [];

		foreach ($terms as $term) {

			if ((int) $term < 0) {
				$negative_term_list[] = abs($term);
			}

		}

		$terms = get_terms(VcLoopSettings::getTaxonomies(), ['include' => array_map('abs', $terms)]);

		foreach ($terms as $t) {
			$operator = in_array((int) $t->term_id, $negative_term_list) ? 'NOT IN' : 'IN';
			$this->args['tax_query'][] = [
				'field'    => 'id',
				'taxonomy' => $t->taxonomy,
				'terms'    => $t->term_id,
				'operator' => $operator,
			];
		}

	}

	protected function parse_tags($value) {

		$this->args['tags'] = $value;

	}

	protected function parse_by_id($value) {

		$in = $not_in = [];
		$ids = $this->stringToArray($value);

		foreach ($ids as $id) {
			$id = (int) $id;

			if ($id < 0) {
				$not_in[] = abs($id);
			} else {
				$in[] = $id;
			}

		}

		$this->args['post__in'] = $in;
		$this->args['post__not_in'] = $not_in;
	}

	public function excludeId($id) {

		if (!isset($this->args['post__not_in'])) {
			$this->args['post__not_in'] = [];
		}

		$this->args['post__not_in'][] = $id;
	}

	protected function stringToArray($value) {

		$valid_values = [];
		$list = preg_split('/\,[\s]*/', $value);

		foreach ($list as $v) {

			if (strlen($v) > 0) {
				$valid_values[] = $v;
			}

		}

		return $valid_values;
	}

	public function build() {

		$db = Db::getInstance();
		$context = Context::getContext();
		$id_lang = $context->language->id;
		$id_company = $context->company->id;

		$sql = "SELECT sbpl.* FROM " . _DB_PREFIX_ . "smart_blog_post sbp INNER JOIN " . _DB_PREFIX_ . "smart_blog_post_lang sbpl ON sbp.id_smart_blog_post=sbpl.id_smart_blog_post";
		$sql .= " LEFT JOIN " . _DB_PREFIX_ . "smart_blog_post_shop sbps ON sbpl.id_smart_blog_post=sbps.id_smart_blog_post";
		$sql .= " LEFT JOIN " . _DB_PREFIX_ . "smart_blog_post_category sbpc ON sbpc.id_smart_blog_post=sbp.id_smart_blog_post";
		$sql .= " LEFT JOIN " . _DB_PREFIX_ . "smart_blog_post_tag sbpt ON sbpt.id_post=sbp.id_smart_blog_post";
		$sql .= " WHERE sbps.id_shop={$id_company} AND sbpl.id_lang={$id_lang} AND sbp.active=1";

		if (isset($this->args['cats']) && !empty($this->args['cats'])) {
			$sql .= " AND sbpc.id_smart_blog_category IN ({$this->args['cats']})";
		}

		if (isset($this->args['tags']) && !empty($this->args['tags'])) {
			$sql .= " AND sbpt.id_tag IN ({$this->args['tags']})";
		}

		if (isset($this->args['orderby']) && !empty($this->args['orderby'])) {

			if ($this->args['orderby'] == 'meta_title' || $this->args['orderby'] == 'link_rewrite') {
				$orderby = "sbpl.{$this->args['orderby']}";
			} else if ($this->args['orderby'] == 'date') {
				$this->args['orderby'] = 'created';
				$orderby = "sbp.{$this->args['orderby']}";
			} else {
				$orderby = "sbp.{$this->args['orderby']}";
			}

			$sql .= " ORDER BY {$orderby}";

			if (isset($this->args['order']) && !empty($this->args['order'])) {
				$sql .= " {$this->args['order']}";
			}

		}

		if (isset($this->args['posts_per_page']) && !empty($this->args['posts_per_page'])) {
			$sql .= " LIMIT {$this->args['posts_per_page']}";
		}

		$results = $db->executeS($sql, true, false);

		foreach ($results as $result_key => $result_val) {
			$results[$result_key]['content'] = $result_val['short_description'];
		}

		return [$this->args, $results];
	}

}
