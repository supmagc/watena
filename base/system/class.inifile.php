<?php

class IniFile extends CacheableFile {
	
	private $m_aData = array();
	private $m_aInheritance = array();
		
	public function make() {
		$aParsing = parse_ini_file($this->getFilePath(), true);
				
		if($aParsing === false) {
			$this->getLogger()->warning('Unable to parse *.ini file: {path}', array('path' => $this->getFilePath()));
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
		if(isset($this->m_aData[$sSection])) {
			$aData = $this->m_aData[$sSection];
			$sInheritance = $this->m_aInheritance[$sSection];
			if($sInheritance) return $this->merge($this->getData($sInheritance), $aData);
			else return $aData;
		}
		else {
			$this->getLogger()->debug('Unknown ini-section \'{section}\' in {file}', array('section' => $sSection, 'file' => $this->getFilePath()));
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
}

?>