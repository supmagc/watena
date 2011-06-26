<?php

abstract class IPCO_ComponentWrapper extends IPCO_Base {
	
	public abstract function tryGetProperty(&$mValue, $sName, $bFirstCall = true);
	public abstract function tryGetMethod(&$mValue, $sName, array $aParams, $bFirstCall = true);

	public static function createComponentWrapper($mComponent, IPCO $oIpco) {
		if(is_object($mComponent)) {
			return new IPCO_ObjectComponentWrapper($mComponent, $oIpco);
		}
		else if(is_array($mComponent)) {
			return new IPCO_ArrayComponentWrapper($mComponent, $oIpco);
		}
		else {
			throw new IPCO_Exception('The provided component is not a valid componenttype.', IPCO_Exception::INVALIDCOMPONENTTYPE);
		}
	}
}

class IPCO_ObjectComponentWrapper extends IPCO_ComponentWrapper {

	private $m_aInstanceProperties = array();
	private $m_aStaticProperties = array();
	private $m_aMethods = array();
	private $m_oComponent = null;
	
	public function __construct($mComponent, IPCO $oIpco) {
		parent::__construct($oIpco);
		
		$this->m_aInstanceProperties = get_object_vars($mComponent);
		$this->m_aStaticProperties = get_class_vars(get_class($mComponent));
		$this->m_aMethods = get_class_methods($mComponent);
		$this->m_oComponent = $mComponent;
	}	
	
	public function tryGetProperty(&$mValue, $sName, $bFirstCall = true) {
		if(array_key_exists($sName, $this->m_aInstanceProperties)) {
			$mValue = $this->m_aInstanceProperties[$sName];
			return true;
		}
		else if(array_key_exists($sName, $this->m_aStaticProperties)) {
			$mValue = $this->m_aStaticProperties[$sName];
			return true;
		}
		else {
			return $bFirstCall ? self::tryGetMethod($mValue, $sName, array(), false) : false;
		}
	}
	
	public function tryGetMethod(&$mValue, $sName, array $aParams, $bFirstCall = true) {
		if(in_array($sName, $this->m_aMethods)) {
			$mValue = call_user_func_array(array($this->m_oComponent, $sName), $aParams);
			return true;
		}
		else {
			return $bFirstCall && count($aParams) == 0 ? self::tryGetProperty($mValue, $sName, false) : false;
		}
	}
}

class IPCO_ArrayComponentWrapper extends IPCO_ComponentWrapper {
	
	private $m_aComponent;
	
	public function __construct($mComponent, IPCO $oIpco) {
		parent::__construct($oIpco);
		
		$this->m_aComponent = $mComponent;
	}
	
	public  function tryGetProperty(&$mValue, $sName, $bFirstCall = true) {
		if(array_key_exists($sName, $this->m_aComponent)) {
			$mValue = $this->m_aComponent[$sName];
			return true;
		}
		else {
			return $bFirstCall ? self::tryGetMethod($mValue, $sName, array(), false) : false;
		}
	}
	
	public function tryGetMethod(&$mValue, $sName, array $aParams, $bFirstCall = true) {
		return false;
	}
}

?>