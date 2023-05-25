<?php

class ComposerEditLayout extends Composer {

    public function render($editor) {

        $data = $this->context->smarty->createTemplate(_EPH_COMPOSER_DIR_  . 'editors/popups/panel_edit_layout.tpl');
		$data->assign(
			[
				'row_layouts' => $editor->row_layouts,
                'editor'         => $editor,
			]
		);
		return $data->fetch();
        
    }
}