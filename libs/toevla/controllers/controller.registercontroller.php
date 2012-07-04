<?php
require_plugin('UserManager');
require_plugin('ToeVla');

class RegisterController extends Controller {

	public function process(Model $oModel = null, View $oView = null) {
		if(UserManager::isValidEmail($oModel->getEmail())) {
			$oUser = User::load(UserManager::getUserIdByEmail($_POST['email']));
			if($oUser) {
				$oEmail = $oUser->getEmail($oModel->getEmail());
				if($oEmail->getVerified()) {
					if($oModel->getPass()) {
						try {
							$oUser = UserManager::login($oUser, $oModel->getPass());
							$oModel->showDone(ToeVla::getNewHash());
						}
						catch(UserInvalidPasswordException $e) {
							$oModel->showInvalidPassword();
						}
						catch(UserNoPasswordException $e) {
							$oModel->showInvalidPassword();
						}
					}
					else {
						$oModel->showLogin();
					}
				}
				else {
					$oModel->showUnverifiedEmail();
					$oEmail->resetVerifier();
					// Send email
				}
			}
			else {
				if($oModel->getPass()) {
					try {
						$oUser = UserManager::register($oModel->getEmail(), $oModel->getPass(), $oModel->getEmail());
						$oModel->showRegisterDone();
					}
					catch(UserInvalidEmailException $e) {
						$oModel->showInvalidEmail();
					}
					catch(UserInvalidNameException $e) {
						$oModel->showInvalidEmail();
					}
					catch(UserInvalidPasswordException $e) {
						$oModel->showRegister();
					}
				}
				else {
					$oModel->showRegister();
				}
			}
		}
		else {
			$oModel->showInvalidEmail();
		}
	}
}

?>