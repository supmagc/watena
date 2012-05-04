<?php
require_controller('UserSessionController');
require_plugin('DatabaseManager');
require_plugin('UserManager');
require_plugin('ToeVla');

class MainController extends UserSessionController {

	public function process(Model $oModel = null, View $oView = null) {
		
		if(UserManager::isLoggedIn()) {
			$oModel->setHash(ToeVla::getNewHash());
			$oModel->setTitle('Flanders Is A Festival - Welcome back ...');
		}
		else {
			$oModel->setTitle('Flanders Is A Festival');
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