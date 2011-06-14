<?php

class TemplateView extends View {
	
	public function render(Model $oModel) {
		$oPlugin = parent::getWatena()->getContext()->getPlugin('TemplateLoader');
		$oTemplate = $oPlugin->load(parent::getConfig('template', 'index.tpl'));
		echo "" . $oTemplate;
	}
	
	public static function getRequirements() {
		return array('plugins' => 'TemplateLoader');
	}
}

?>