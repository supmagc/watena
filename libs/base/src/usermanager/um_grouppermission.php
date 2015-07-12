<?php namespace Watena\Libs\Base;

class GroupPermission extends DbObject {

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
	
	public static function load(Group $oGroup, Permission $oPermission) {
		return (!empty($oGroup) && !empty($oPermission)) ? DbObject::loadObject('GroupPermission', 'group_permission', array(
				'groupId' => $oGroup->getId(),
				'permissionId' => $oPermission->getId()
		)) : false;
	}
	
	public static function create(Group $oGroup, Permission $oPermission, $nValue = UserManager::PERMISSION_UNDEFINED) {
		return (!empty($oGroup) && !empty($oPermission) && UserManager::isValidPermission($nValue)) ? DbObject::createObject('GroupPermission', 'group_permission', array(
				'groupId' => $oGroup->getId(),
				'permissionId' => $oPermission->getId(),
				'value' => $nValue
		)) : false;
	}
	
}

?>