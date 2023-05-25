<?php

class RevSliderLoadBalancer {

	public $servers = [];

	/**
	 * set the server list on construct
	 **/
	public function __construct() {

		$this->servers = RevLoader::get_option('revslider_servers', []);
		$this->servers = (empty($this->servers)) ? ['themepunch.tools'] : $this->servers;

	}

	/**
	 * get the url depending on the purpose, here with key, you can switch do a different server
	 **/
	public function get_url($purpose, $key = 0, $force_http = false) {

		$url = ($force_http) ? 'http://' : 'https://';
		$use_url = (!isset($this->servers[$key])) ? reset($this->servers) : $this->servers[$key];

		switch ($purpose) {
		case 'updates':
			$url .= 'updates.';
			break;
		case 'templates':
			$url .= 'templates.';
			break;
		case 'library':
			$url .= 'library.';
			break;
		default:
			return false;
		}

		$url .= $use_url;

		return $url;
	}

	/**
	 * move the server list, to take the next server as the one currently seems unavailable
	 **/
	public function move_server_list() {

		$servers = $this->servers;
		$a = array_shift($servers);
		$servers[] = $a;

		$this->servers = $servers;
		RevLoader::update_option('revslider_servers', $servers);
	}

	/**
	 * call an themepunch URL and retrieve data
	 **/
	public function call_url($url, $data, $subdomain = 'updates', $force_http = false) {

		global $wp_version;

		//add version if not passed
		$data['version'] = (!isset($data['version'])) ? urlencode(RS_REVISION) : $data['version'];

		$done = false;
		$count = 0;

		do {
			$server = $this->get_url($subdomain, 0, $force_http);
			$request = RevLoader::wp_remote_post($server . '/' . $url, [
				'user-agent' => 'revphp;' . RevLoader::get_bloginfo('url'),
				'body'       => $data,
				'timeout'    => 45,
			]);

			$response_code = RevLoader::wp_remote_retrieve_response_code($request);

			if ($response_code == 200) {
				$done = true;
			} else {
				$this->move_server_list();
			}

			$count++;
		} while ($done == false && $count < 5);

		return $request;
	}

}

?>