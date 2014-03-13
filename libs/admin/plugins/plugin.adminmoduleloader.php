<?php

class AdminModuleLoader extends Plugin {
	
	private $m_nLastChange = 0;
	private $m_aWatchPaths = array();
	
	private $m_aModuleItems = array();
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
		foreach($this->m_aModuleItems as $oItem) {
			$this->m_aMappings[$oItem->getMapping()] = $oItem->getDefaultModuleTab();
			foreach($oItem->getModuleTabs() as $oTab) {
				$this->m_aMappings[$oTab->GetMapping()] = $oTab;
			}
			array_assure($this->m_aCategories, array($oItem->getCategory(), $oItem->getName()), $oItem);
		}
				
		if(isset($this->m_aMappings[$this->getDefaultModuleItemMapping()])) {
			$this->m_aMappings['/'] = $this->m_aMappings[$this->getDefaultModuleItemMapping()];
		}
		else {
			$this->getLogger()->error('The default mapping \'{mapping}\' for the AdminLoader could not be found.', array('mapping' => $this->getDefaultModuleItemMapping()));
		}
	}
	
	public function getDefaultModuleItemMapping() {
		return $this->getConfig('MAPPING_DEFAULT', '/main');
	}
	
 	public function getCategories() {
 		return $this->m_aCategories;
 	}
	
	public function getModuleItems() {
		return $this->m_aModuleItems;
	}
	
	public function getByMapping($sMapping) {
		$sMapping = Encoding::toLower($sMapping);
		return isset($this->m_aMappings[$sMapping]) ? $this->m_aMappings[$sMapping]  : false;
	}
	
	private function parseModuleFile($sFilePath) {
		$oXml = new SimpleXMLElement($sFilePath, null, true);
		foreach($oXml->menu as $oXmlMenu) {
			$sName = '' . $oXmlMenu['name'];
			$sCategory = '' . $oXmlMenu->category;
			$sDefaultTab = '' . $oXmlMenu->defaulttab;
			$sDescription = '' . $oXmlMenu->description;
			$oMenu = new AdminModuleItem($sName, $sCategory, $sDescription, $sDefaultTab);
			foreach($oXmlMenu->tab as $oXmlTab) {
				$sTabName = '' . $oXmlTab['name'];
				$sTabDescription = '' . $oXmlTab->description;
				$sTabContent = '' . $oXmlTab->content;
				$sTabContentType = !empty($sTabContent) ? ('' . $oXmlTab->content['type']) : '';
				$oMenu->addModuleTab(new AdminModuleTab($oMenu, $sTabName, $sTabDescription, $sTabContentType, $sTabContent));
			}
			$this->m_aModuleItems []= $oMenu;
		}
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
}

?>