<?php

class ComposerShortCode_vc_images_carousel extends ComposerShortCode_vc_gallery {

	protected static $carousel_index = 1;

	public function __construct($settings) {

		parent::__construct($settings);
		
		$this->jsCssScripts();
	}

	public function jsCssScripts() {
        
	}

	public static function getCarouselIndex() {

		return self::$carousel_index++ . '-' . time();
	}

	protected function getSliderWidth($size) {


		$width = '100%';

		$vc_manager = ephenyx_manager();



		if (isset($vc_manager->image_sizes[$size]) && !empty($vc_manager->image_sizes[$size])) {

			preg_match_all('/\d+/', $vc_manager->image_sizes[$size], $matches);

			if (count($matches[0]) > 1) {
				$width = $matches[0][0] . 'px';
			}

		}

		return $width;
	}

}
