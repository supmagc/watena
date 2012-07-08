<?php

class IniFile extends CacheableFile {
	
	private $m_aData = array();
	
	public function make() {
		// Read the data
		$aData = parse_ini_file($this->getFilePath(), true);
		
		// Make sure the data returned is a valid array
		if($aData === false) {
			$this->getLogger()->warning('Unableto parse *.ini file: {path}', array('path' => $this->getFilePath()));
		}
		else {
			// Process the array
			$aData = array_change_key_case($aData, CASE_LOWER);
			$this->m_aData = $aData;
		}
	}
	
	public function getData() {
		return $this->m_aData;
	}
}

?>