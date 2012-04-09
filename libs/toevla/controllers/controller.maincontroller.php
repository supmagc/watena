<?php
require_controller('UserSessionController');
require_plugin('UserManager');
require_plugin('Socializer');

class MainController extends UserSessionController {

	public function process(Model $oModel, View $oView) {
		if(UserManager::isLoggedIn()) {
			$oModel->setHash('MyTestHash');
			$oModel->setTitle('Start to play');
		}
		else {
			$oModel->setTitle('Login first');
		}
	}
}

?>