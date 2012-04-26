<?php
require_controller('UserSessionController');
require_plugin('UserManager');
require_plugin('ToeVla');

class MainController extends UserSessionController {

	public function process(Model $oModel = null, View $oView = null) {
		
		if(UserManager::isLoggedIn()) {
			$oModel->setHash(ToeVla::getNewHash());
			$oModel->setTitle('Flanders Is A Festival - Welcome back ...');
		}
		else {
			$oModel->setTitle('Flanders Is A Festival');
		}
	}
}

?>