<?php
require_controller('UserSessionController');
require_plugin('DatabaseManager');
require_plugin('UserManager');

class FacebookCallbackController extends UserSessionController {

	public function process(Model $oModel, View $oView) {
		try {
			if(UserManager::getFacebookProvider()->connect() && UserManager::connectToProvider(UserManager::getFacebookProvider())) {
				$this->redirect('/');
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
		catch(Exception $e) {
			$this->display('Some error occured!');
		}
	}
}

?>