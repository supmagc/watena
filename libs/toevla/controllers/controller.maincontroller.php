<?php
require_controller('UserSessionController');
require_plugin('DatabaseManager');
require_plugin('UserManager');
require_plugin('ToeVla');

class MainController extends UserSessionController {

	public function process(Model $oModel = null, View $oView = null) {
		
		if($this->getWatena()->getMapping()->getPart(0) == 'verify') {
			$sMail = null;
			$nVerifyUser = -1;
			$nVerifyMail = -1;
			if(isset($_GET['verify_user']) && isset($_GET['verify_user_id'])) {
				$nVerifyUser = 0;
				$oUser = User::load($_GET['verify_user_id']);
				if($oUser && $oUser->verify($_GET['verify_user'])) {
					$nVerifyUser = 1;
					$sMail = $oUser->getEmail()->getEmail();
				}
			}
			if(isset($_GET['verify_mail']) && isset($_GET['verify_mail_id'])) {
				$nVerifyMail = 0;
				$oMail = UserEmail::load($_GET['verify_mail_id']);
				if($oMail && $oMail->verify($_GET['verify_mail'])) {
					$nVerifyMail = 1;
					$sMail = $oMail->getEmail();
				}
			}
			if($nVerifyUser != 0 && $nVerifyMail != 0 && $sMail) {
				$oModel->showVerifier($sMail);
			}
		}
		
		if(UserManager::isLoggedIn()) {
			$oModel->setHash(ToeVla::getNewHash());
		}
		
		if($this->getWatena()->getMapping()->getPart(0) == 'iframe' && Encoding::length($this->getWatena()->getMapping()->getPart(1)) == 32) {
			$oStatement = DatabaseManager::getConnection('toevla')->getTable('festival', 'hash')->select($this->getWatena()->getMapping()->getPart(1));
			if(($oData = $oStatement->fetchObject()) !== false) {
				$oModel->setHubId($oData->genreId);
				$oModel->setFestivalId($oData->ID);
			}
		}
	}
}

?>