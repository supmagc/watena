<?php

abstract class AdminPlugin extends Plugin implements IAdminGeneratable {
	
	private $m_aMappingsStr = array();
	private $m_aMappingsReg = array();
	
	public abstract function requestMappings();
	
	public final function make(array $aMembers) {
		$this->requestMappings();
	}
	
	public function addMapping($sMapping, $cbMethod, $bIsRegex = false) {
		if($bIsRegex)
			$this->m_aMappingsReg []= array('reg' => $sMapping, 'cb' => $cbMethod);
		else
			$this->m_aMappingsStr[Encoding::toLower($sMapping)] = $cbMethod;
	}
	
	public function generate(AdminModuleContentRequest $oRequest, AdminModuleContentResponse $oResponse) {
		$sRequest = Encoding::toLower($oRequest->getMapping());
		foreach($this->m_aMappingsStr as $sMapping => $cbMethod) {
			if($sMapping == $sRequest)
				return call_user_func(array($this, $cbMethod), $oRequest, $oResponse);
		}
		foreach($this->m_aMappingsReg as $aMapping) {
			if(Encoding::regMatch($aMapping['reg'], $sRequest))
				return call_user_func(array($this, $aMapping['cb']), $oRequest, $oResponse);
		}
		$oResponse->setError('No mapping method found', 'No valid admin method callback could be found for the admin mapping you provided.');
	}
}

?>