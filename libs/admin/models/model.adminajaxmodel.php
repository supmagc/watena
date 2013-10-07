<?php
require_model('AjaxModel');

class AdminAjaxModel extends AjaxModel {

	public function tester($m) {
		$this->alert("$m\n".$this->val);
	}
}

?>