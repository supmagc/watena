<?php

class AdminModuleLoader extends Plugin {
	
	private $m_nLastChange = 0;
	private $m_aWatchPaths = array();
	
	private $m_aModules = array();
	private $m_aMappings = array();
	private $m_aCategories = array();
	
	public function validate() {
		foreach($this->m_aWatchPaths as $sWatchPath) {
			if(filemtime($sWatchPath) > $this->m_nLastChange) return false;
		}
		return true;
	}
	
	public function make(array $aMembers) {
		$aPaths = parent::getWatena()->getContext()->getLibraryPaths();
		foreach($aPaths as $sPath) {
			if(($sPath = realpath($sPath . '/modules/')) !== false) {
				$this->m_aWatchPaths []= $sPath;
				$this->m_nLastChange = max($this->m_nLastChange, filemtime($sPath));
				$aFileNames = scandir($sPath);
				foreach($aFileNames as $sFileName) {
					$sFilePath = $sPath . '/' . $sFileName;
					if(Encoding::regFind('^module\\.([a-zA-Z-0-9_]+)\.xml$', $sFileName) && is_file($sFilePath) && is_readable($sFilePath)) {
						if(!is_file($sFilePath)) {
							$this->getLogger()->warning('A path \'{path}\' found to be a module-file is not an actual file.', array('path' => $sFilePath));
						}
						else if(!is_readable($sFilePath)) {
							$this->getLogger()->warning('A module-file \'{file}\' is not readable.', array('file' => $sFilePath));
						}
						else {
							$this->m_aWatchPaths []= $sFilePath;
							$this->m_nLastChange = max($this->m_nLastChange, filemtime($sFilePath));
							$this->getLogger()->info('Parsing module-file \'{file}\'.', array('file' => $sFilePath));
							$this->parseModuleFile($sFilePath);
						}
					}
				}
			}
		}
		
		// Parse all the menus into a mapping retrieval listing
		foreach($this->m_aModules as $oModule) {
			foreach($oModule->getModuleItems() as $oModuleItem) {
				$this->m_aMappings[$oModuleItem->getMapping()] = $oModuleItem->getDefaultModuleTab();
				foreach($oModuleItem->getModuleTabs() as $oModuleTab) {
					$this->m_aMappings[$oModuleTab->GetMapping()] = $oModuleTab;
				}
				array_assure($this->m_aCategories, array($oModuleItem->getCategory(), $oModuleItem->getName()), $oModuleItem);
			}
		}
				
		if(isset($this->m_aMappings[$this->getDefaultMapping()])) {
			$this->m_aMappings['/'] = $this->m_aMappings[$this->getDefaultMapping()];
		}
		else {
			$this->getLogger()->error('The default mapping \'{mapping}\' for the AdminLoader could not be found.', array('mapping' => $this->getDefaultMapping()));
		}
	}
	
	public function getDefaultMapping() {
		return self::convertToMapping($this->getConfig('MODULEITEM_DEFAULT', 'dashboard'));
	}
	
 	public function getCategories() {
 		return $this->m_aCategories;
 	}
	
	public function getModules() {
		return $this->m_aModules;
	}
	
	public function getByMapping($sMapping) {
		$sMapping = Encoding::toLower($sMapping);
		return isset($this->m_aMappings[$sMapping]) ? $this->m_aMappings[$sMapping]  : false;
	}
	
	private function parseModuleFile($sFilePath) {
		$oXml = new SimpleXMLElement($sFilePath, null, true);
		$sName = '' . $oXml['name'];
		$sVersion = '' . $oXml->version;
		$sDescription = '' . $oXml->description;
		$oModule = new AdminModule($sName, $sVersion, $sDescription);
		foreach($oXml->menu as $oXmlMenu) {
			$sName = '' . $oXmlMenu['name'];
			$sCategory = '' . $oXmlMenu->category;
			$sDefaultTab = '' . $oXmlMenu->defaulttab;
			$sDescription = '' . $oXmlMenu->description;
			$oModuleItem = new AdminModuleItem($oModule, $sName, $sCategory, $sDescription, $sDefaultTab);
			foreach($oXmlMenu->tab as $oXmlTab) {
				$sTabName = '' . $oXmlTab['name'];
				$sTabDescription = '' . $oXmlTab->description;
				$sTabData = '' . $oXmlTab->content;
				$sTabType = !empty($sTabData) ? ('' . $oXmlTab->content['type']) : '';
				$oModuleItem->addModuleTab(new AdminModuleTab($oModuleItem, $sTabName, $sTabDescription, AdminModuleContent::process($sTabType, $sTabData)));
			}
			$oModule->addModuleItem($oModuleItem);
		}
		$this->m_aModules []= $oModule;
	}
	
	/**
	 * Retrieve version information of this plugin.
	 * The format is an associative array as follows:
	 * 'major' => Major version (int)
	 * 'minor' => Minor version (int)
	 * 'build' => Build version (int)
	 * 'state' => Naming of the production state
	 */
	public function getVersion() {
		return array(
			'major' => 0,
			'minor' => 1,
			'build' => 1,
			'state' => 'dev'
		);
	}
	
	public static function convertToMapping($sName) {
		return '/' . Encoding::regReplace('[^-a-z0-9_]', '_', Encoding::toLower($sName));
	}
}

?>