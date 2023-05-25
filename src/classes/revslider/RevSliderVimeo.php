<?php

class RevSliderVimeo extends RevSliderFunction {

	/**
	 * Stream Array
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $stream    Stream Data Array
	 */
	private $stream;

	/**
	 * Transient seconds
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      number    $transient Transient time in seconds
	 */
	private $transient_sec;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $api_key	Youtube API key.
	 */
	public function __construct($transient_sec = 1200) {

		$this->transient_sec = $transient_sec;
	}

	/**
	 * Get Vimeo User Videos
	 *
	 * @since    1.0.0
	 */
	public function get_vimeo_videos($type, $value) {

		//call the API and decode the response
		$url = 'https://vimeo.com/api/v2/';
		$url .= ($type == 'user') ? $value . '/videos.json' : $type . '/' . $value . '/videos.json';

		$transient_name = 'revslider_' . md5($url);

		if ($this->transient_sec > 0 && false !== ($data = RevLoader::get_transient($transient_name))) {
			return ($data);
		}

		$rsp = json_decode(RevLoader::wp_remote_fopen($url));
		RevLoader::set_transient($transient_name, $rsp, $this->transient_sec);

		return $rsp;
	}

}

// End Classy