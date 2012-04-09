<?php
require_plugin('Socializer');

class TwitterMailController extends Controller {

	public function process(Model $oModel, View $oView) {
		if(Socializer::twitter()->login()) {	
			$this->redirect('/twitter/mail');
		
			$aData = Socializer::twitter()->api('/account/verify_credentials.json', 'GET', array('skip_status' => 1));
			$sScreenName = $aData['screen_name'];
			$sName = $aData['name'];
			$nId = $aData['id'];
		
			// Check is user allready exists
			// If not create one,
			// If it does, load the existing one
		
		}
		else {
			$this->redirect('/');
		}
	}
}

?>