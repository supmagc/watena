<?php

class IniParser extends CacheableData {
	
	const FALLBACK = 'default';
	
	private $m_aData = array();
	private $m_aInheritance = array();
		
	public function make(array $aMembers) {
		$sContent = $this->getConfig('content', '');
		$sFile = $this->getConfig('file', null);
		$aFiles = $this->getConfig('files', array());
		
		if($sFile) $sContent .= "\n" . file_get_contents($sFile);
		foreach($aFiles as $sFile) $sContent .= "\n" . file_get_contents($sFile);
		
		$aParsing = parse_ini_string($sContent, true);
				
		if($aParsing === false) {
			$this->getLogger()->warning('Unable to parse *.ini data.', array('data' => $this->m_sContent));
		}
		else {
			foreach($aParsing as $sSection => $aData) {
				if(is_array($aData)) {
					$this->processSection($sSection, $aData);
				}
				else {
					$this->getLogger()->warning('Invalid/none-section data found in *.ini file: {path}', array('path' => $this->getFilePath()));
				}
			}
		}
	}
	
	public function getData($sSection) {
		if(!isset($this->m_aData[$sSection])) {
			$this->getLogger()->debug('Unknown ini-section \'{section}\', thus reverting to fallback \'{fallback}\'.', array('section' => $sSection, 'fallback' => self::FALLBACK));
			$sSection = self::FALLBACK;
		}
		if(isset($this->m_aData[$sSection])) {
			$aData = $this->m_aData[$sSection];
			$sInheritance = $this->m_aInheritance[$sSection];
			if($sInheritance) return $this->merge($this->getData($sInheritance), $aData);
			else return $aData;
		}
		else {
			$this->getLogger()->warning('Unknown ini-section \'{section}\' in {file}', array('section' => $sSection, 'file' => $this->getFilePath()));
			return array();
		}
	}
	
	private function processSection($sSection, array $aData) {
		$sInheritance = null;
		$sSection = Encoding::toLower($sSection);
		$nExtendIndex = Encoding::indexOf($sSection, ':');
		
		if($nExtendIndex !== false) {
			$sInheritance = Encoding::trim(Encoding::substring($sSection, $nExtendIndex + 1));
			$sSection = Encoding::trim(Encoding::substring($sSection, 0, $nExtendIndex));
		}
		
		if(!isset($this->m_aData[$sSection])) {
			$this->m_aData[$sSection] = array();
		}
		
		if(isset($this->m_aInheritance[$sSection]) && $this->m_aInheritance[$sSection] !== $sInheritance) {
			$this->getLogger()->warning("Invalid ini-section inheritance, was '{section}' was previously defined as '{old}', and now '{new}'.", array(
				'section' => $sSection,
				'old' => $this->m_aInheritance[$sSection],
				'new' => $sInheritance
			));
		}
		
		$this->m_aInheritance[$sSection] = $sInheritance;		
		$this->m_aData[$sSection] = $this->merge($this->m_aData[$sSection], $this->processData($aData));
	}
	
	private function processData(array $aData) {
		$aReturn = array();
		foreach($aData as $sKey => $mValue) {
			array_assure($aReturn, explode_trim('.', Encoding::toLower($sKey)), $mValue, true);
		}
		return $aReturn;
	}
	
	private function merge(array $a, array $b) {
		foreach($b as $k => $v)
			$a[$k] = (is_assoc($a[$k]) && is_assoc($v)) ? $this->merge($a[$k], $v) : $v;
		return $a;
	}
	
	public static function createFromFile($sFileName) {
		$sFilePath = parent::getWatena()->getPath($sFileName);
		$oLoader = new CacheLoader('IniParser');
		$oLoader->addPathDependency($sFilePath);
		return $oLoader->get(array('file' => $sFilePath));
	}
	
	public static function createFromFiles(array $aFileNames) {
		$oLoader = new CacheLoader('IniParser');
		foreach($aFileNames as $sFileName) {
			$sFilePath = parent::getWatena()->getPath($sFileName);
			$oLoader->addPathDependency($sFilePath, true);
		}
		return $oLoader->get(array('files' => $aFileNames));
	}
	
	public static function createFromContent($sContent) {
		$oLoader = new CacheLoader('IniParser');
		$oLoader->addDataDependency($sContent);
		return $oLoader->get(array('content' => $sContent));
	}
}

?>