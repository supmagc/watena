<?php

class IPCO_ComponentWrapper extends IPCO_Base {

	private $m_aInstanceProperties = array();
	private $m_aStaticProperties = array();
	
	public function __construct($mCommponent, IPCO $ipco) {
		$this->m_ipco = $ipco;
		
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
}

?>