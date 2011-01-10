<?php

class TemplateView extends View {
	
	public function render(Model $oModel) {
		echo 'here comes the template view';
	}
	
	public static function getRequirements() {
		return array('plugins' => 'TemplateLoader');
	}
}

?>