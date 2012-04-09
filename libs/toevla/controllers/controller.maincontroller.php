<?php
require_controller('UserSessionController');
require_plugin('UserManager');
require_plugin('Socializer');

class MainController extends UserSessionController {

	public function process(Model $oModel, View $oView) {

		echo '<pre>';
		print_r($_SESSION);
		print_r($_COOKIE);
		echo '</pre>';
		
		if(UserManager::isLoggedIn()) {
			$oModel->setHash('MyTestHash');
			$oModel->setTitle('Start to play');
		}
		else {
			$this->display(UserManager::getFacebookProvider()->getConnectUrl('http://flandersisafestival.dev/facebook/callback'));
			$oModel->setTitle('Login first');
		}
	}
}

?>