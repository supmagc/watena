<?php
require_plugin('DatabaseManager');
require_plugin('ToeVla');

class ConfigModel extends Model {
	
	public function getConfigData() {
		$aData = array(
			'debug'				=> Encoding::indexOf($this->getWatena()->getMapping()->getHost(), '.com') ? (isset($_COOKIE['debug']) ? 1 : 0) : 1,
			'host'				=> '' . new Mapping(''),
			'developmentLogin'	=> '' . new Mapping('/debug/login'),
			'logoutUrl'			=> '' . new Mapping('/logout'),
			'smartFoxSettings'	=> Encoding::indexOf($this->getWatena()->getMapping()->getHost(), '.com') ? 'ONLINE_TESTING' : ($_SERVER['COMPUTERNAME'] == 'JELLE-MONSTER' ? 'LOCALHOST' : 'GRINTERNAL'),
			'imageUrlPrefix'	=> '' . new Mapping('/files/toevla/festival'),
			'picasaUrlPrefix'	=> '' . new Mapping('/request/picasa'),
			'flickrUrlPrefix'	=> '' . new Mapping('/request/flickr'),
			'listingsUrlPrefix'	=> '' . new Mapping('/request/listings'),
			'twitterUrlPrefix'	=> '' . new Mapping('/request/twitter'),
			'facebookUrlPrefix'	=> '' . new Mapping('/request/facebook'),
			'audioUrlPrefix'	=> '' . new Mapping('/files/toevla/audio'),
			'loggerUrl'			=> '' . new Mapping('/debug/logger'),
			'analytics'			=> 'UA-31352229-1'
		);
		$oStatement = DatabaseManager::getConnection('toevla')->select('game_config');
		foreach($oStatement as $aRow) {
			$aData[$aRow['name']] = $aRow['value'];
		}
		return $aData;
	}
}

?>