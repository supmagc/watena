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
		else if($this->getWatena()->getMapping()->getLocal() == '/debug/logger') {
			$sFile = $this->getWatena()->getPath('l/toevla/files/unity/debug.log');
			$sTrace = implode("\r\n\t", explode_trim("\n", $_POST['trace']));
			$sData = sprintf("%s\t%s\t%s at:\r\n\t%s\r\n", $this->getWatena()->getTime()->formatDefault(), $_SERVER['REMOTE_ADDR'], $_POST['message'], $sTrace);
			if(file_assure($sFile))
				file_put_contents($sFile, $sData, FILE_APPEND);
			if(filesize($sFile) > 1024*1024*10) {
				rename($sFile, $sFile . '.' . $this->getWatena()->getTime()->getTimestamp());
			}
		}
		else {
			echo 'UNKNOWN';
		}
	}
}

?>