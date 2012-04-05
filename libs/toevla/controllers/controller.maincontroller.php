<?php
require_controller('UserSessionController');
require_plugin('Socializer');

class MainController extends UserSessionController {

	public function process(Model $oModel, View $oView) {
		
		$bIsLoggedIn = false;
		
		if(Socializer::twitter()->isLoggedIn()) {
			$bIsLoggedIn = true;
		}
		
		$oFacebookUser = Socializer::facebook()->getUser();
		if($oFacebookUser) {
			try {
				$aFacebookUserProfile = Socializer::facebook()->api('/me');
				$bIsLoggedIn = true;
			}
			catch(FacebookApiException $e) {
				$this->getLogger()->exception($e);
			}
		}
		
		if(false && $bIsLoggedIn) {
			$oModel->setHash('MyTestHash');
			$oModel->setTitle('Start to play');
		}
		else {
			$oModel->setTitle('Login first');
		}
	}
}

?>