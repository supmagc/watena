<?php
require_plugin('UserManager');
require_plugin('ToeVla');

class RegisterController extends Controller {

	public function process(Model $oModel = null, View $oView = null) {
		if($oModel->getEmail() && $oModel->getPass()) {
			try{
				UserManager::loginByEmail($oModel->getEmail(), $oModel->getPass());
				$oModel->showDone('Logged in!', 'You will be redirected within the next seconds.', ToeVla::getNewHash());
			}
			catch(UserInvalidEmailException $e) {
				$oModel->showEmail('Invalid email!', 'Try another email-adress!');
			}
			catch(UserUnknownEmailException $e) {
				$oUser = UserManager::register($oModel->getEmail(), $oModel->getPassword(), $oModel->getEmail());
				$sVerifierA = $oUser->makeVerifier();
				$sVerifierB = $oUser->getEmail($oModel->getEmail())->makeVerifier();
				$oModel->showMessage('Registered!', 'Check your mail for your verification message.');
				// Send email
			}
			catch(UserUnverifiedEmailException $e) {
				$sVerifier = $e->getEmail()->makeVerifier();
				$oModel->showError('Unverified email!', 'This email-adress is not yet verified. Check your mailbox for a verification message.');
				// Send Mail
			}
			catch(UserUnverifiedUserException $e) {
				$sVerifier = $e->getUser()->makeVerifier();
				$oModel->showError('Unverified user!', 'You are not yet verified. Check your mailbox for a verification message.');
				// Send Mail
			}
			catch(UserNoPasswordException $e) {
				$e->getUser()->setPassword($oModel->getPass());
				UserManager::setLoggedInUser($e->getUser());
				$oModel->setDone('Logged in!', 'You will be redirected within the next seconds.', ToeVla::getNewHash());
			}
			catch(UserInvalidPasswordException $e) {
				$oModel->showPass('Wrong password!', 'Try another password!', true);
			}
		}
		else if($oModel->getEmail()) {
			if(!UserManager::isValidEmail($oModel->getEmail())) {
				$oModel->showEmail('Invalid email', 'Try another email-adress!', true);
			}
			else {
				$oModel->showPass('Enter password!', 'If you are a recurring user, enter your previous password.<br />If you are a new user, create a new password.');
			}
		}
	}
}

?>