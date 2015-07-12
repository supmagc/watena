<?php namespace Watena\Libs\Base\Views;
require_plugin('TemplateLoader');

class TemplateView extends View {
	
	public function headers(Model $oModel = null) {
		$this->headerContentType('text/plain');
	}
	
	public function render(Model $oModel = null) {
		$oTemplate = TemplateLoader::load(parent::getConfig('template', 'index.tpl'));
		$oGenerator = $oTemplate->createTemplateClass();
		$oGenerator->componentPush($oModel);
		echo $oGenerator->getContent(true);
	}
}

?>