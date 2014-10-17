<?php

class AdminHelp extends AdminPlugin {

	public function requestMappings() {
		$this->addMapping('/about/plugins', 'generatePlugins');
		$this->addMapping('/about/modules', 'generateModules');
	}
	
	public function generatePlugins(AdminModuleContentRequest $oRequest, AdminModuleContentResponse $oResponse) {
		$aData = array();
		$aMatches = array();
		$aPositions = array();
		foreach(parent::getWatena()->getContext()->getLibraries() as $sLibraryName) {
			$sLibraryPath = PATH_LIBS . DIRECTORY_SEPARATOR . $sLibraryName . DIRECTORY_SEPARATOR . 'plugins';
			$aPluginFiles = scandir($sLibraryPath);
			foreach($aPluginFiles as $sPluginFile) {
				if(Encoding::regFind('^plugin\.([a-z0-9]*)\.php$', $sPluginFile, $aMatches, $aPositions)) {
					$sPluginName = $aMatches[1];
					//parent::getWatena()->getContext()->loadPlugin($aMatches[1]);
					try {
						$oPlugin = parent::getWatena()->getContext()->getPlugin($sPluginName);
						$aData []= array(
							'name' => get_class($oPlugin),
							'version' => version2string($oPlugin->getVersion()),
							'library' => $sLibraryName
						);
					}
					catch(Excepion $e) {}
				}
			}
		}
		
		$oResponse->setContentTemplate('admin.help.plugins.tpl', $aData);
	}

	public function generateModules(AdminModuleContentRequest $oRequest, AdminModuleContentResponse $oResponse) {
		$aModules = Admin::getLoader()->getModules();
		$oResponse->setContentTemplate('admin.help.modules.tpl', $aModules);
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