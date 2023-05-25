<?php
r
class ComposerShortCode_vc_carousel extends ComposerShortCode_VC_Posts_Grid {

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
}