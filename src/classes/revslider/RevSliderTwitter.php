<?php

class RevSliderTwitterApi extends RevSliderFunction {

	public $bearer_token;
	// Default credentials
	public $args = [
		'consumer_key'    => 'default_consumer_key',
		'consumer_secret' => 'default_consumer_secret',
	];
	// Default type of the resource and cache duration
	public $query_args = [
		'type'  => 'statuses/user_timeline',
		'cache' => 1800,
	];

	public $has_error = false;

	/**
	 * WordPress Twitter API Constructor
	 *
	 * @param array $args
	 */
	public function __construct($args = [], $transient_sec = 1200) {

		if (is_array($args) && !empty($args)) {
			$this->args = array_merge($this->args, $args);
		}

		if (!$this->bearer_token = RevLoader::get_option('twitter_bearer_token')) {
			$this->bearer_token = $this->get_bearer_token();
		}

		$this->query_args['cache'] = $transient_sec;
	}

	/**
	 * Get the token from oauth Twitter API
	 *
	 * @return string Oauth Token
	 */
	private function get_bearer_token() {

		$bearer_token_credentials = $this->get_val($this->args, 'consumer_key') . ':' . $this->get_val($this->args, 'consumer_secret');
		$bearer_token_credentials_64 = base64_encode($bearer_token_credentials);

		$args = [
			'method'      => 'POST',
			'timeout'     => 5,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => [
				'Authorization'   => 'Basic ' . $bearer_token_credentials_64,
				'Content-Type'    => 'application/x-www-form-urlencoded;charset=UTF-8',
				'Accept-Encoding' => 'gzip',
			],
			'body'        => ['grant_type' => 'client_credentials'],
			'cookies'     => [],
		];

		$response = RevLoader::wp_remote_post('https://api.twitter.com/oauth2/token', $args);

		//track
		//if(RevLoader::is_wp_error($response) || 200 != $response['response']['code'])

		if (200 != $response['response']['code']) {
			return $this->bail($this->l(Can\'t get the bearer token, check your credentials'), $response);
		}

		$result = json_decode($this->get_val($response, 'body'));

		RevLoader::update_option('twitter_bearer_token', $this->get_val($result, 'access_token'));

		return $this->get_val($result, 'access_token');
	}

	/**
	 * Query twitter's API
	 *
	 * @uses $this->get_bearer_token() to retrieve token if not working
	 *
	 * @param string $query Insert the query in the format "count=1&include_entities=true&include_rts=true&screen_name=micc1983!
	 * @param array $query_args Array of arguments: Resource type (string) and cache duration (int)
	 * @param bool $stop Stop the query to avoid infinite loop
	 *
	 * @return bool|object Return an object containing the result
	 */
	public function query($query, $query_args = [], $stop = false) {

		if ($this->has_error) {
			return false;
		}

		if (is_array($query_args) && !empty($query_args)) {
			$this->query_args = array_merge($this->query_args, $query_args);
		}

		$transient_name = 'wta_' . md5($query);

		if ($this->get_val($this->query_args, 'cache', 0) > 0 && false !== ($data = RevLoader::get_transient($transient_name))) {
			return json_decode($data);
		}

		$args = [
			'method'      => 'GET',
			'timeout'     => 5,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => [
				'Authorization'   => 'Bearer ' . $this->bearer_token,
				'Accept-Encoding' => 'gzip',
			],
			'body'        => null,
			'cookies'     => [],
		];

		$response = RevLoader::wp_remote_get('https://api.twitter.com/1.1/' . $this->get_val($this->query_args, 'type') . '.json?' . $query, $args);
		//track
		//if(RevLoader::is_wp_error($response) || 200 != $response['response']['code']){

		if (200 != $response['response']['code']) {

			if (!$stop) {
				$this->bearer_token = $this->get_bearer_token();
				return $this->query($query, $this->query_args, true);
			} else {
				return $this->bail($this->l(BearerTokenisgood, checkyourquery'), $response);
			}

		}

		RevLoader::set_transient($transient_name, $response['body'], $this->query_args['cache']);

		return json_decode($response['body']);
	}

	/**
	 * Let'smanageerrors
					 *
					 * WP_DEBUGhastobesettotruetoshowerrors
					 *
					 * @paramstring $error_textErrormessage
					 * @paramstring $error_objectServerresponse or wp_error
					 *  /
					private function bail($error_text, $error_object = '') {

						$this->has_error = true;

						if (RevLoader::is_wp_error($error_object)) {
							$error_text .= ' - Wp Error: ' . $error_object->get_error_message();
						} else
						if (!empty($error_object) && isset($error_object['response']['message'])) {
							$error_text .= ' ( Response: ' . $error_object['response']['message'] . ')';
						}

						trigger_error($error_text, E_USER_NOTICE);
					}

				}
