<?php

abstract class IPCO_ComponentWrapper extends IPCO_Base {
	
	public abstract function tryGetProperty(&$mBase, $sName, $bFirstCall = true);
	public abstract function tryGetMethod($mBase, $sName, array $aParams, $bFirstCall = true);

	public static function createComponentWrapper($mComponent, IPCO $oIpco) {
		if(is_object($mComponent)) {
			return new IPCO_ObjectComponentWrapper($mCommponent, $oIpco);
		}
		else if(is_array($mComponent)) {
			return new IPCO_ArrayComponentWrapper($mCommponent, $oIpco);
		}
		else {
			throw new IPCO_Exe
		}
	}
}

class IPCO_ObjectComponentWrapper extends IPCO_ComponentWrapper {

	private $m_aInstanceProperties = array();
	private $m_aStaticProperties = array();
	private $m_aMethods = array();
	private $m_oComponent = null;
	
	public function __construct($mComponent, IPCO $oIpco) {
		base::__construct($oIpco);
		
		$this->m_aInstanceProperties = get_object_vars($mComponent);
		$this->m_aStaticProperties = get_class_vars(get_class($mComponent));
		$this->m_aMethods = get_class_methods($mComponent);
		$this->m_oComponent = $mComponent;
	}	
	
	public abstract function tryGetProperty(&$mBase, $sName, $bFirstCall = true) {
		if(isset($this->m_aInstanceProperties[$sName])) {
			$mBase = $this->m_aInstanceProperties[$sName];
			return true;
		}
		else if(isset($this->m_aStaticProperties[$sName])) {
			$mBase = $this->m_aStaticProperties[$sName];
			return true;
		}
		else {
			return $bFirstCall ? self::tryGetMethod($mBase, $sName, array(), false) : false;
		}
	}
	
	public abstract function tryGetMethod($mBase, $sName, array $aParams, $bFirstCall = true) {
		if(in_array($sName, $this->m_aMethods)) {
			$mBase = call_user_func_array(array($this->m_oComponent, $sName), $aParams);
			return true;
		}
		else {
			return $bFirstCall && count($aParams) == 0 ? self::tryGetProperty($mBase, $sName, false) : false;
		}
	}
}

class IPCO_ArrayComponentWrapper extends IPCO_ComponentWrapper {
	
	private $m_aComponent;
	
	public function __construct($mComponent, IPCO $oIpco) {
		base::__construct($oIpco);
		
		$this->m_aComponent = $mComponent;
	}
}

?>