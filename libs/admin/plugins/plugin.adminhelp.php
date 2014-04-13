<?php

class AdminHelp extends AdminPlugin {

	public function requestMappings() {
		$this->addMapping('/about/plugins', 'generatePlugins');
		$this->addMapping('/about/modules', 'generateModules');
	}
	
	public function generatePlugins(AdminModuleContentRequest $oRequest, AdminModuleContentResponse $oResponse) {
		$oResponse->setContentTemplate('admin.help.plugins.tpl', array());
	}

	public function generateModules(AdminModuleContentRequest $oRequest, AdminModuleContentResponse $oResponse) {
		$oResponse->setContentText('MODULES');
	}
	
	public function getVersion() {
		return array(
			'major' => 0,
			'minor' => 1,
			'build' => 0,
			'state' => 'dev'
		);
	}
}

?>