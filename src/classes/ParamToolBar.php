<?php
/**
 * Class ParamToolBar
 *
 * @since 2.1.0.0
 */
class ParamToolBar {	
    
    public $items = [];	

	public function __construct() {}	

	public function buildToolBar() {
        
       return ['items' => $this->items];
	}
    
}
