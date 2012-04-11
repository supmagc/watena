<?php
require_controller('UserSessionController');
require_plugin('DatabaseManager');
require_plugin('UserManager');
require_plugin('ToeVla');

class FacebookCallbackController extends UserSessionController {

	public function process(Model $oModel = null, View $oView = null) {
		try {
			if(UserManager::connectToProvider(UserManager::getProviderFacebook())) {
				$oModel->setHash(ToeVla::getNewHash());
			}
			else {
				$this->display('No facebookuser found!');
			}
		}
		catch(UserDuplicateEmailException $e){
			$this->display('Duplicate email found!');
		}
		catch(UserDuplicateNameException $e){
			$this->display('Duplicate name found!');
		}
	}
}

?>