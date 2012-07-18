<?php
define('NLOGGER', true);
define('NWATENA', true);
include '../base/watena.php';

class IniFile extends CacheableFile {
	
	private $m_aData = array();
	private $m_aInheritance = array();
	
	public function getConfig($sSection) {
		if(isset($this->m_aData[$sSection])) {
			$aData = $this->m_aData[$sSection];
			$sInheritance = $this->m_aInheritance[$sSection];
			if($sInheritance) return $this->merge($this->getConfig($sInheritance), $aData);
			else return $aData;
		}
		else {
			return false;
		}
	}
	
	public function parse($sFilePath) {	
		$aParsing = parse_ini_file($sFilePath, true);
		foreach($aParsing as $sSection => $aData) {
			$this->processSection($sSection, $aData);
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
			
		}
		
		$this->m_aInheritance[$sSection] = $sInheritance;		
		$this->m_aData[$sSection] = $this->merge($this->m_aData[$sSection], $this->processData($aData));
	}
	
	private function processData(array $aData) {
		$aReturn = array();
		foreach($aData as $sKey => $mValue) {
			array_assure($aReturn, explode_trim('.', $sKey), $mValue, true);
		}
		return $aReturn;
	}
	
	private function merge(array $a, array $b) {
		foreach($b as $k => $v)
			$a[$k] = (is_assoc($a[$k]) && is_assoc($v)) ? $this->merge($a[$k], $v) : $v;
		return $a;
	}
}

$oTest = new IniFile();
$oTest->parse('./test.ini');

dump($oTest->getConfig('tester'));
?>