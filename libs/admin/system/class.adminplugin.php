<?php

abstract class AdminPlugin extends Plugin implements IAdminGeneratable {
	
	private $m_aMappingsStr = array();
	private $m_aMappingsReg = array();
	
	public abstract function requestMappings();
	
	public final function make(array $aMembers) {
		$this->requestMappings();
	}
	
	public function addMapping($sMapping, $sCallback, $bIsRegex = false) {
		if($bIsRegex)
			$this->m_aMappingsReg []= array('reg' => $sMapping, 'cb' => $sCallback);
		else
			$this->m_aMappingsStr[$sMapping] = $sCallback;
	}
	
	public function generate(AdminModuleData $oData) {
	}
}

?>