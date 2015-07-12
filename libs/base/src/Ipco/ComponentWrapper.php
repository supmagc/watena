<?php namespace Watena\Libs\Base\Ipco;

abstract class ComponentWrapper extends Base implements \Iterator {
	
	public abstract function tryGetProperty(&$mValue, $sName, $bFirstCall = true);
	public abstract function tryGetMethod(&$mValue, $sName, array $aParams, $bFirstCall = true);
	public abstract function getFirst();
	public abstract function getLast();
	
	public static function createComponentWrapper($mComponent, IPCO $oIpco) {
		if(is_object($mComponent)) {
			return new ObjectComponentWrapper($mComponent, $oIpco);
		}
		else if(is_array($mComponent)) {
			return new ArrayComponentWrapper($mComponent, $oIpco);
		}
		else {
			throw new Exception('The provided component is not a valid componenttype.', Exception::INVALID_COMPONENTTYPE);
		}
	}
}
