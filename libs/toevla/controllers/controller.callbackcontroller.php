<?php
require_plugin('UserManager');
require_plugin('ToeVla');

class CallbackController extends Controller {

	public function process(Model $oModel = null, View $oView = null) {
		echo '<pre>';
		print_r($_SESSION);
		echo '</pre>';
		
		try {
			$sName = UserManager::getConnectionProvider($this->getConfig('provider'))->getConnectionName();
			if(isset($_POST['name'])) {
				$sName = Encoding::trim($_POST['name']);
				$this->display($sName);
			}
			if(UserManager::connectToProvider(UserManager::getConnectionProvider($this->getConfig('provider')), $sName)) {
				$oModel->setHash(ToeVla::getNewHash());
				$oModel->showSucces();
			}
			else {
				$oModel->showFailed();
			}
			$oModel->setName($sName);
		}
		catch(UserDuplicateNameException $e){
			$oModel->setName(UserManager::getConnectionProvider($this->getConfig('provider'))->getConnectionName());
			$oModel->showDuplicateName('Name is allready in use!');
		}
		catch(UserDuplicateEmailException $e){
			$oModel->showDuplicateEmail();
		}
		catch(UserDuplicateConnectionException $e){
			$oModel->showDuplicateConnection();
		}
	}
}

?>