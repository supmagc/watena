<?php namespace Watena\Libs\Base;

class UserPermission extends DbObject {
	
	public function getValue() {
		return $this->getDataValue('value');
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

	public static function load(User $oUser, Permission $oPermission) {
		return (!empty($oUser) && !empty($oPermission)) ? DbObject::loadObject('UserPermission', 'user_permission', array(
			'userId' => $oUser->getId(),
			'permissionId' => $oPermission->getId()
		)) : false;
	}
	
	public static function create(User $oUser, Permission $oPermission, $nValue = UserManager::PERMISSION_UNDEFINED) {
		return (!empty($oUser) && !empty($oPermission) && UserManager::isValidPermission($nValue)) ? DbObject::createObject('UserPermission', 'user_permission', array(
			'userId' => $oUser->getId(),
			'permissionId' => $oPermission->getId(),
			'value' => $nValue
		)) : false;
	}
}

?>