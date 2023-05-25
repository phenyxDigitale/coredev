<?php

class ComposerPostSeetings {

	protected $editor;
    public $context;

	public function __construct($editor) {
        global $smarty;
        $this->context = Context::getContext();
		$this->editor = $editor;
	}

	public function editor() {

		return $this->editor;
	}

	public function render() {
        $data = $this->context->smarty->createTemplate(_EPH_COMPOSER_DIR_  . 'editors/popups/panel_post_settings.tpl');
        $data->assign(
			[
				'box' => $this,
			]
		);
		return $data->fetch();
		
	}
}