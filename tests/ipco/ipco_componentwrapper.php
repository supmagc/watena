<?php

class IPCO_ComponentWrapper extends IPCO_Base {

	private $m_aInstanceProperties = array();
	private $m_aStaticProperties = array();
	
	public function __construct($mCommponent, IPCO $oIpco) {
		base::__construct($oIpco);
		
		if(is_object($mCommponent)) {
			$oReflector = new ReflectionClass($mCommponent);
			$aProperties = $oReflector->getProperties();			
			
			foreach($aProperties as $oProperty) {
				if($oProperty->isPublic()) {
					if($oProperty->isStatic()) {
						$this->m_aStaticProperties[$oProperty->getName()] = true;
					}
					else {
						$this->m_aInstanceProperties[$oProperty->getName()] = true;
					}
				}
			}
		}
	}	
	
	public function tryGetProperty(&$mBase, $sName) {
		// Try as property for objects or slice for arrays
		
		return false;
	}
	
	public function tryGetMethod($mBase, $sName, array $aParams) {
		return false;
	}
}

?>