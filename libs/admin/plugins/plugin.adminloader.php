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
		$oXml = new XMLReader();
		$oXml->open($sFilePath);
		while($oXml->read()) {
			if($oXml->nodeType == XMLReader::ELEMENT && $oXml->name == 'menu') {
				$this->m_aMenus []= $this->parseXmlMenu($oXml);
			}
		}
	}
	
	private function parseXmlMenu(XMLReader $oReader) {
		$sDescription = null;
		$sDefaultTab = null;
		$sCategory = null;
		$sName = null;
		$aTabs = array();
		if($oReader->moveToAttribute('name')) {
			$sName = $oReader->value;
		}
		while($oReader->read()) {
			if($oReader->nodeType == XMLReader::ELEMENT) {
				if($oReader->name == 'description' && $oReader->read()) {
					$sDescription = $oReader->value;
				}
				else if($oReader->name == 'defaulttab' && $oReader->read()) {
					$sDefaultTab = $oReader->value;
				}
				else if($oReader->name == 'category' && $oReader->read()) {
					$sCategory = $oReader->value;
				}
				if($oReader->name == 'tab') {
					$aTabs []= $this->parseXmlTab($oReader);
				}
			}
			else if($oReader->nodeType == XMLReader::END_ELEMENT && $oReader->name == 'menu') {
				return new AdminMenu($sName, $sCategory, $sDescription, $sDefaultTab, $aTabs);
			}
		}
	}
	
	private function parseXmlTab(XMLReader $oReader) {
		$sDescription = null;
		$sContent = null;
		$sType = null;
		$sName = null;
		if($oReader->moveToAttribute('name')) {
			$sName = $oReader->value;
		}
		while($oReader->read()) {
			if($oReader->nodeType == XMLReader::ELEMENT) {
				if($oReader->name == 'description' && $oReader->read()) {
					$sDescription = $oReader->value;
				}
				else if($oReader->name == 'content' && $oReader->moveToAttribute('type')) {
					$sType = $oReader->name;
					if($oReader->read()) {
						$sContent = $oReader->value;
					}
				}
			}
			else if($oReader->nodeType == XMLReader::END_ELEMENT && $oReader->name == 'tab') {
				return new AdminTab($sName, $sDescription, $sType, $sContent);
			}
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