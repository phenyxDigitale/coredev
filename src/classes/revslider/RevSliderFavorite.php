<?php

class RevSliderFavorite extends RevSliderFunction {

	public $allowed = [
		'plugintemplates',
		'plugintemplateslides',
		'plugins',
		'pluginslides',
		'svgs',
		'images',
		'videos',
		'objects',
		'fonticons',
	];

	/**
	 * change the setting of a favorization
	 **/
	public function set_favorite($do, $type, $id) {

		$fav = RevLoader::get_option('rs_favorite', []);
		$id = RevLoader::esc_attr($id);

		if (in_array($type, $this->allowed)) {

			if (!isset($fav[$type])) {
				$fav[$type] = [];
			}

			$key = array_search($id, $fav[$type]);

			if ($key === false) {

				if ($do == 'add') {
					$fav[$type][] = $id;
				}

			} else {

				if ($do == 'remove') {
					unset($fav[$type][$key]);
				}

			}

		}

		RevLoader::update_option('rs_favorite', $fav);

		return $fav;
	}

	/**
	 * get a certain favorite type
	 **/
	public function get_favorite($type) {

		$fav = RevLoader::get_option('rs_favorite', []);
		$list = [];

		if (in_array($type, $this->allowed)) {
			$list = $this->get_val($fav, $type, []);
		}

		return $list;
	}

	/**
	 * return if certain element is in favorites
	 **/
	public function is_favorite($type, $id) {

		$favs = $this->get_favorite($type);

		return (array_search($id, $favs) !== false) ? true : false;
	}

}

?>