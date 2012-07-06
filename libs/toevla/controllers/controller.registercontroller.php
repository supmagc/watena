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
				try {
					$oUser = UserManager::register($oModel->getEmail(), $oModel->getPass(), $oModel->getEmail());
					$nIdA = $oUser->getId();
					$nIdB = $oUser->getEmail($oModel->getEmail())->getId();
					$sVerifierA = $oUser->makeVerifier();
					$sVerifierB = $oUser->getEmail($oModel->getEmail())->makeVerifier();
					$oModel->showMessage('Registered!', 'Check your mail for your verification message.');
					$sLink = 'http://flandersisafestival.com/verify?verify_user='.$sVerifierA.'&verify_mail='.$sVerifierB.'&verify_user_id='.$nIdA.'&verify_mail_id='.$nIdB;
					$oMail = new Mail();
					$oMail->setTo($oModel->getEmail());
					$oMail->setFrom('info@flandersisafestival.com', 'Flanders Is A Festival');
					$oMail->setSubject('Account Verification');
					$oMail->setContentHtml('<h2>Account Verification</h2>
						<p>It seems you just registered at <a href="http://flandersisafestival.com">www.flandersisafestival.com</a>.</p>
						<p>If this is you, click the link to continue:<br /><a href="'.$sLink.'">'.$sLink.'</a>.</p>
						<p>If you didn\'t register at <a href="http://flandersisafestival.com">www.flandersisafestival.com</a>, you can ignore this message.</p><br /><br /></ br>
						Sincerely,<br />FLanders Is A Festival - The Game');
					$oMail->convertHtmlToText();
					$oMail->send();
				}
				catch(UserException $e) {
					$oModel->showError('Registration Error!', 'We couldn\'t register your account. Maybe you can try again?');
				}
			}
			catch(UserUnverifiedEmailException $e) {
				$nId = $e->getEmail()->getId();
				$sVerifier = $e->getEmail()->makeVerifier();
				$oModel->showError('Unverified email!', 'This email-adress is not yet verified. Check your mailbox for a verification message.');
				$sLink = 'http://flandersisafestival.com/verify?verify_mail='.$sVerifier.'&verify_mail_id='.$nId;
				$oMail = new Mail();
				$oMail->setTo($oModel->getEmail());
				$oMail->setFrom('info@flandersisafestival.com', 'Flanders Is A Festival');
				$oMail->setSubject('Account Verification');
				$oMail->setContentHtml('<h2>Account Verification</h2>
					<p>It seems you need to verify your email-adress at <a href="http://flandersisafestival.com">www.flandersisafestival.com</a>.</p>
					<p>If this is you, click the link to continue:<br /><a href="'.$sLink.'">'.$sLink.'</a>.</p>
					<p>If you didn\'t register at <a href="http://flandersisafestival.com">www.flandersisafestival.com</a>, you can ignore this message.</p><br /><br /></ br>
					Sincerely,<br />FLanders Is A Festival - The Game');
				$oMail->convertHtmlToText();
				$oMail->send();
			}
			catch(UserUnverifiedUserException $e) {
				$nId = $e->getUser()->getId();
				$sVerifier = $e->getUser()->makeVerifier();
				$oModel->showError('Unverified user!', 'You are not yet verified. Check your mailbox for a verification message.');
				$sLink = 'http://flandersisafestival.com/verify?verify_user='.$sVerifier.'&verify_user_id='.$nId;
				$oMail = new Mail();
				$oMail->setTo($oModel->getEmail());
				$oMail->setFrom('info@flandersisafestival.com', 'Flanders Is A Festival');
				$oMail->setSubject('Account Verification');
				$oMail->setContentHtml('<h2>Account Verification</h2>
					<p>It seems you need to verify your account at <a href="http://flandersisafestival.com">www.flandersisafestival.com</a>.</p>
					<p>If this is you, click the link to continue:<br /><a href="'.$sLink.'">'.$sLink.'</a>.</p>
					<p>If you didn\'t register at <a href="http://flandersisafestival.com">www.flandersisafestival.com</a>, you can ignore this message.</p><br /><br /></ br>
					Sincerely,<br />FLanders Is A Festival - The Game');
				$oMail->convertHtmlToText();
				$oMail->send();
			}
			catch(UserNoPasswordException $e) {
				$e->getUser()->setPassword($oModel->getPass());
				UserManager::setLoggedInUser($e->getUser());
				$oModel->showDone('Logged in!', 'You will be redirected within the next seconds.', ToeVla::getNewHash());
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
		else if(isset($_GET['email'])) {
			$oModel->showSucces('Verification Done!', 'Your account has been verified. You can now login as VIP by using: ' . $_GET['email']);
		}
	}
}

?>