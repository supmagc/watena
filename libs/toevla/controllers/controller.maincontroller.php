<?php
require_controller('UserSessionController');
require_plugin('UserManager');
require_plugin('ToeVla');

class MainController extends UserSessionController {

	public function process(Model $oModel = null, View $oView = null) {
		
		$oTime = Time::createSystemTime();
		$this->display(var_export($oTime->getTimestamp(), true));
		$oTime->setTimezone('Europe/Brussels');
		$this->display(var_export($oTime->getTimestamp(), true));
		
		if(UserManager::isLoggedIn()) {
			$oModel->setHash(ToeVla::getNewHash());
			$oModel->setTitle('Start to play');
		}
		else {
			$oModel->setTitle('Login first');
		}
	}
}

?>