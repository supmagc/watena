<?php
require_plugin('UserManager');
require_plugin('ToeVla');

class ConnectController extends Controller {

	public function process(Model $oModel = null, View $oView = null) {
		$sName = UserManager::getConnectionProvider($this->getConfig('provider'))->getConnectionName();
		if(isset($_POST['name'])) {
			$sName = Encoding::trim($_POST['name']);
			$this->display($sName);
		}
		$oModel->setName($sName);
		try {
			if(UserManager::connectToProvider(UserManager::getConnectionProvider($this->getConfig('provider')), $sName)) {
				$oModel->setHash(ToeVla::getNewHash());
				$oModel->showSucces();
			}
			else {
				$oModel->showFailed();
			}
		}
		catch(UserDuplicateUserException $e) {
			$oModel->showDuplicateLogin();
		}
		catch(UserDuplicateNameException $e) {
			$oModel->showDuplicateName();
		}
		catch(UserDuplicateEmailException $e) {
			$oModel->showDuplicateEmail();
		}
		catch(UserInvalidNameException $e) {
			$oModel->showInvalidName();
		}
		catch(UserInvalidEmailException $e) {
			$oModel->showInvalidEmail();
		}
		catch(UserUnverifiedEmailException $e) {
			$oModel->showUnverifiedEmail();
		}
	}
}

?>