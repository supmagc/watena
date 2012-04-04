<?php
//require_controller('SessionController');
require_plugin('Socializer');

class MainController extends Controller {

	public function process(Model $oModel, View $oView) {
		
		$this->display(Socializer::twitter()->getLoginUrl());
		
		$oFacebookUser = Socializer::facebook()->getUser();
		if($oFacebookUser) {
			try {
				$aFacebookUserProfile = Socializer::facebook()->api('/me');
				$oModel->setHash('MyTestHash');
			}
			catch(FacebookApiException $e) {
				$this->getLogger()->exception($e);
			}
		}
		
		$oModel->setTitle('TestTitle');
	}
}

?>