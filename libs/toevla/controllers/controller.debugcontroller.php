<?php
require_plugin('UserManager');
require_plugin('toevla');

class DebugController extends Controller {
	
	public function process(Model $oModel = null, View $oView = null) {
		if($this->getWatena()->getMapping()->getLocal() == '/debug/login') {
			$nId = UserManager::getUserIdByName($_GET['user']);
			$oUser = User::load($nId);
			UserManager::setLoggedInUser($oUser);
			echo ToeVla::getNewHash();
		}
		else {
			echo 'UNKNOWN';
		}
	}
}

?>