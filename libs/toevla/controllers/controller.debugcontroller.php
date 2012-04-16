<?php
require_plugin('UserManager');
require_plugin('toevla');

class DebugController extends Controller {
	
	public function process(Model $oModel = null, View $oView = null) {
		if($this->getWatena()->getMapping()->getLocal() == '/debug/login') {
			$nId = UserManager::getUserIdByName($_GET['user']);
			$oUser = User::load($nId);
			UserManager::setLoggedInUser($oUser);
			echo ToeVla::getNewHash();
		}
		else if($this->getWatena()->getMapping()->getLocal() == '/debug/switch') {
			$nExpiration = Time::create()->add(new Interval(0, 0, 1, 0, 0, 0))->getTimestamp();
			setcookie('debug', 'enabled', $nExpiration, $this->getWatena()->getMapping()->getOffset() ?: '/');
			$this->display('Debugging is enabled !');
		}
		else {
			echo 'UNKNOWN';
		}
	}
}

?>