<?php

class RevSliderEventsManager extends RevSliderFunction {

	public function __construct() {

		RevLoader::add_filter('revslider_get_posts_by_category', [$this, 'add_post_query'], 10, 2);
	}

	/**
	 * check if events class exists
	 */
	public static function isEventsExists() {

		return (defined('EM_VERSION') && defined('EM_PRO_MIN_VERSION')) ? true : false;
	}

	/**
	 * get sort by list
	 * @before: RevSliderEventsManager::getArrFilterTypes()
	 */
	public static function get_filter_types() {

		return [
			'none'      => $this->l('All Events'),
			'today'     => $this->l('Today'),
			'tomorrow'  => $this->l('Tomorrow'),
			'future'    => $this->l('Future'),
			'past'      => $this->l('Past'),
			'month'     => $this->l('This Month'),
			'nextmonth' => $this->l('Next Month'),
		];
	}

	/**
	 * get meta query
	 * @before: RevSliderEventsManager::getWPQuery()
	 */
	public static function get_query($filter_type, $sort_by) {

		$response = [];
		$dayMs = 60 * 60 * 24;
		//track
		//$time				= current_time('timestamp');
		$time = time();
		$todayStart = strtotime(date('Y-m-d', $time));
		$todayEnd = $todayStart + $dayMs - 1;
		$tomorrowStart = $todayEnd + 1;
		$tomorrowEnd = $tomorrowStart + $dayMs - 1;
		$start_month = strtotime(date('Y-m-1', $time));
		$end_month = strtotime(date('Y-m-t', $time)) + 86399;
		$next_month_middle = strtotime('+1 month', $time); //get the end of this month + 1 day
		$start_next_month = strtotime(date('Y-m-1', $next_month_middle));
		$end_next_month = strtotime(date('Y-m-t', $next_month_middle)) + 86399;
		$query = [];

		switch ($filter_type) {
		case 'none': //none
			break;
		case 'today':
			$query[] = ['key' => '_start_ts', 'value' => $todayEnd, 'compare' => '<='];
			$query[] = ['key' => '_end_ts', 'value' => $todayStart, 'compare' => '>='];
			break;
		case 'future':
			$query[] = ['key' => '_start_ts', 'value' => $time, 'compare' => '>'];
			break;
		case 'tomorrow':
			$query[] = ['key' => '_start_ts', 'value' => $tomorrowEnd, 'compare' => '<='];
			$query[] = ['key' => '_end_ts', 'value' => $todayStart, 'compare' => '>='];
			break;
		case 'past':
			$query[] = ['key' => '_end_ts', 'value' => $todayStart, 'compare' => '<'];
			break;
		case 'month':
			$query[] = ['key' => '_start_ts', 'value' => $end_month, 'compare' => '<='];
			$query[] = ['key' => '_end_ts', 'value' => $start_month, 'compare' => '>='];
			break;
		case 'nextmonth':
			$query[] = ['key' => '_start_ts', 'value' => $end_next_month, 'compare' => '<='];
			$query[] = ['key' => '_end_ts', 'value' => $start_next_month, 'compare' => '>='];
			break;
		default:
			$this->throw_error('Wrong event filter');
			break;
		}

		if (!empty($query)) {
			$response['meta_query'] = $query;
		}

		//convert sortby

		switch ($sort_by) {
		case 'event_start_date':
			$response['orderby'] = 'meta_value_num';
			$response['meta_key'] = '_start_ts';
			break;
		case 'event_end_date':
			$response['orderby'] = 'meta_value_num';
			$response['meta_key'] = '_end_ts';
			break;
		}

		return $response;
	}

	/**
	 * get event post data in array.
	 * if the post is not event, return empty array
	 * @before: RevSliderEventsManager::getEventPostData()
	 */
	public static function get_event_post_data($postID) {

		if (self::isEventsExists() == false) {
			return [];
		}

		$postType = get_post_type($postID);

		if ($postType != EM_POST_TYPE_EVENT) {
			return [];
		}

		$f = new RevSliderFunction();
		$event = new EM_Event($postID, 'post_id');
		$location = $event->get_location();
		$ev = $event->to_array();
		$loc = $location->to_array();
		$date_format = RevLoader::get_option('date_format');
		$time_format = RevLoader::get_option('time_format');

		$response = [
			'id'                 => $f->get_val($ev, 'event_id'),
			'start_date'         => date_format(date_create_from_format('Y-m-d', $f->get_val($ev, 'event_start_date')), $date_format),
			'end_date'           => date_format(date_create_from_format('Y-m-d', $f->get_val($ev, 'event_end_date')), $date_format),
			'start_time'         => date_format(date_create_from_format('H:i:s', $f->get_val($ev, 'event_start_time')), $time_format),
			'end_time'           => date_format(date_create_from_format('H:i:s', $f->get_val($ev, 'event_end_time')), $time_format),
			'location_name'      => $f->get_val($loc, 'location_name'),
			'location_address'   => $f->get_val($loc, 'location_address'),
			'location_slug'      => $f->get_val($loc, 'location_slug'),
			'location_town'      => $f->get_val($loc, 'location_town'),
			'location_state'     => $f->get_val($loc, 'location_state'),
			'location_postcode'  => $f->get_val($loc, 'location_postcode'),
			'location_region'    => $f->get_val($loc, 'location_region'),
			'location_country'   => $f->get_val($loc, 'location_country'),
			'location_latitude'  => $f->get_val($loc, 'location_latitude'),
			'location_longitude' => $f->get_val($loc, 'location_longitude'),
		];

		return $response;
	}

	/**
	 * get events sort by array
	 */
	public static function getArrSortBy() {

		return [
			'event_start_date' => $this->l('Event Start Date'),
			'event_end_date'   => $this->l('Event End Date'),
		];
	}

	/**
	 * triggered if we receive posts by categories (RevSliderSlider::get_posts_by_categories())
	 **/
	public function add_post_query($data, $slider) {

		$filter_type = $slider->get_param('events_filter', 'none');

		if (self::isEventsExists()) {
			$data['addition'] = RevSliderEventsManager::get_query($filter_type, $this->get_val($data, 'sort_by'));
		}

		return $data;
	}

}

//$rs_em = new RevSliderEventsManager();
?>