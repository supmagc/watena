<?php
require_controller('UserSessionController');
require_plugin('UserManager');
require_plugin('ToeVla');

class MainController extends UserSessionController {

	public function process(Model $oModel, View $oView) {
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