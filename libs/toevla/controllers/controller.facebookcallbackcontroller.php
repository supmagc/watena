<?php
require_controller('UserSessionController');
require_plugin('DatabaseManager');
require_plugin('Socializer');

class FacebookCallbackController extends UserSessionController {

	public function process(Model $oModel, View $oView) {
		
		$oFacebookUser = Socializer::facebook()->getUser();
		if($oFacebookUser) {
			try {
				$aData = Socializer::facebook()->api('/me');
				
				$sName = $aData['name'];
				$sEmail = $aData['email'];
				$sUsername = $aData['username'];
				$nFacebookId = $aData['id'];
				
				$oDatabase = DatabaseManager::getConnection();
				$oDatabase->getPdo()->beginTransaction();
				$oResult = $oDatabase->select('user_facebook', $nFacebookId, 'facebookId');
				if($oResult->rowCount() === 0) {
					$nUserId = $oDatabase->insert('user', array(
						'type' => 0,
						'name' => $sUsername
					), false);
					$nUserFacebookId = $oDatabase->insert('user_facebook', array(
						'name' => $sName,
						'email' => $sEmail,
						'username' => $sUsername,
						'facebookId' => $nFacebookId,
						'userId' => $nUserId,
						'accessToken' => Socializer::facebook()->getAccessToken()
					), false);
					$oDatabase->update('user', $nUserId, array('userFacebookId' => $nUserFacebookId));
				}
				$oDatabase->getPdo()->commit();
				$this->redirect('/');
			}
			catch(FacebookApiException $e) {
				$this->getLogger()->exception($e);
			}
		}
		else {
			$this->display('No facebookuser found!');
		}
	}
}

?>