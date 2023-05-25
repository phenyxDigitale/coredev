<?php


class ComposerShortCode_vc_row_inner extends ComposerShortCode_vc_row {

	protected function getFileName() {

		return 'vc_row';
	}

	public function template($content = '') {

		return $this->contentAdmin($this->atts);
	}
}