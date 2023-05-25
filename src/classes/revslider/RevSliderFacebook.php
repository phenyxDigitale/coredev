<?php

class RevSliderFacebook extends RevSliderFunction {

	private $stream;

	private $transient_sec;

	public function __construct($transient_sec = 1200) {

		$this->transient_sec = $transient_sec;
	}

	public function get_user_from_url($user_url) {

		$theid = str_replace('https', '', $user_url);
		$theid = str_replace(['https', 'http', '://', 'www.', 'facebook', '.com', "/"], '', $user_url);
		$theid = explode('?', $theid);

		return trim($theid[0]);
	}

	public function get_photo_sets($user_id, $access_token, $item_count = 10) {

		$url = "https://graph.facebook.com/$user_id/albums?access_token=" . $access_token;
		$photo_sets_list = json_decode(RevLoader::wp_remote_fopen($url));

		if (!empty($photo_sets_list->error->message)) {
			return ["error", $photo_sets_list->error->message];
		}

		return $this->get_val($photo_sets_list, 'data');
	}

	public function get_photo_set_photos($photo_set_id, $access_token, $item_count = 10) {

		$url = "https://graph.facebook.com/" . $photo_set_id . "/photos?fields=photos&access_token=" . $access_token . "&fields=id,from,message,picture,images,link,name,icon,privacy,type,status_type,application,created_time,updated_time,is_hidden,is_expired,comments.limit(1).summary(true),likes.limit(1).summary(true)";

		$transient_name = 'revslider_' . md5($url);

		if ($this->transient_sec > 0 && false !== ($data = RevLoader::get_transient($transient_name))) {
			return $data;
		}

		$photo_set_photos = json_decode(RevLoader::wp_remote_fopen($url));

		$data = $this->get_val($photo_set_photos, 'data');

		if ($data !== '') {
			RevLoader::set_transient($transient_name, $data, $this->transient_sec);
		}

		return $data;
	}

	public function get_photo_set_photos_options($user_url, $current_album, $access_token, $item_count = 99) {

		$user_id = $this->get_user_from_url($user_url);
		$photo_sets = $this->get_photo_sets($user_id, $access_token, 999);

		if (isset($photo_sets[0]) && $photo_sets[0] == "error") {
			return $photo_sets;
		}

		if (empty($current_album)) {
			$current_album = '';
		}

		$return = [];

		if (is_array($photo_sets)) {

			foreach ($photo_sets as $photo_set) {
				$return[] = '<option title="' . $this->get_val($photo_set, 'name') . '" ' . RevLoader::selected($this->get_val($photo_set, 'id'), $current_album, false) . ' value="' . $this->get_val($photo_set, 'id') . '">' . $this->get_val($photo_set, 'name') . '</option>"';
			}

		}

		return $return;
	}

	public function get_photo_feed($user, $access_token, $item_count = 10) {

		$url = "https://graph.facebook.com/$user/feed?access_token=" . $access_token . "&fields=full_picture,picture,attachments{media,media_type,url},icon,message,likes.limit(1).summary(true),comments.limit(1).summary(true)";

		$transient_name = 'revslider_' . md5($url);

		if ($this->transient_sec > 0 && false !== ($data = RevLoader::get_transient($transient_name))) {
			return $data;
		}

		$feed = json_decode(RevLoader::wp_remote_fopen($url));

		$data = $this->get_val($feed, 'data');

		if ($data !== '') {
			RevLoader::set_transient($transient_name, $data, $this->transient_sec);
		}

		return $data;
	}

	private function decode_facebook_url($url) {

		$url = str_replace('u00253A', ':', $url);
		$url = str_replace('\u00255C\u00252F', '/', $url);
		$url = str_replace('u00252F', '/', $url);
		$url = str_replace('u00253F', '?', $url);
		$url = str_replace('u00253D', '=', $url);
		$url = str_replace('u002526', '&', $url);

		return $url;
	}

}
