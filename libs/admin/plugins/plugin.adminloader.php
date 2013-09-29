<?php

class AdminLoader extends Plugin {
	
	private $m_nLastChange = 0;
	private $m_aWatchPaths = array();
	
	private $m_aMenus = array();
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
		foreach($this->m_aMenus as $oMenu) {
			$this->m_aMappings[$oMenu->getMapping()] = $oMenu->getDefaultTab();
			foreach($oMenu->getTabs() as $oTab) {
				$this->m_aMappings[$oMenu->getMapping() . $oTab->GetMapping()] = $oTab;
			}
			array_assure($this->m_aCategories, array($oMenu->getCategory(), $oMenu->getName()), $oMenu);
		}
				
		if(isset($this->m_aMappings[$this->getDefaultMapping()])) {
			$this->m_aMappings['/'] = $this->m_aMappings[$this->getDefaultMapping()];
		}
		else {
			$this->getLogger()->error('The default mapping \'{mapping}\' for the AdminLoader could not be found.', array('mapping' => $this->getDefaultMapping()));
		}
	}
	
	public function getDefaultMapping() {
		return $this->getConfig('MAPPING_DEFAULT', '/main/dashboard');
	}
	
 	public function getCategories() {
 		return $this->m_aCategories;
 	}
	
	public function getMenus() {
		return $this->m_aMenus;
	}
	
	private function parseModuleFile($sFilePath) {
		$oXml = new SimpleXMLElement($sFilePath, null, true);
		foreach($oXml->menu as $oXmlMenu) {
			$sName = '' . $oXmlMenu['name'];
			$sCategory = '' . $oXmlMenu->category;
			$sDefaultTab = '' . $oXmlMenu->defaulttab;
			$sDescription = '' . $oXmlMenu->description;
			$oMenu = new AdminMenu($sName, $sCategory, $sDescription, $sDefaultTab);
			foreach($oXmlMenu->tab as $oXmlTab) {
				$sTabName = '' . $oXmlTab['name'];
				$sTabDescription = '' . $oXmlTab->description;
				$sTabContent = '' . $oXmlTab->content;
				$sTabContentType = !empty($sTabContent) ? ('' . $oXmlTab->content['type']) : '';
				$oMenu->addTab(new AdminTab($sTabName, $sTabDescription, $sTabContentType, $sTabContent));
			}
			$this->m_aMenus []= $oMenu;
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