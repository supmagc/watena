<?php namespace Watena\Libs\Base;

class Permission extends DbObject {
	
	public function getName() {
		return $this->getDataValue('name');
	}
	
	public function getValue() {
		return $this->getDataValue('value');
	}
	
	public function getTimestamp() {
		return $this->getDataValue('timestamp');
	}
	
	public function setName($sName) {
		if(UserManager::isValidName($sName)) {
			$this->setDataValue('name', $sName);
			return true;
		}
		return false;
	}
	
	public function setValue($nValue) {
		if($nValue === true) $nValue = UserManager::PERMISSION_GRANTED;
		if($nValue === false) $nValue = UserManager::PERMISSION_REVOKED;
		if(UserManager::isValidPermission($nValue)) {
			$this->setDataValue('value', $nValue);
			return true;
		}
		return false;
	}
}

?>