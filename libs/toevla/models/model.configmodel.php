<?php
require_plugin('DatabaseManager');

class ConfigModel extends Model {
	
	public function getConfigData() {
		$aData = array(
			'host'				=> '' . new Mapping(''),
			'developmentLogin'	=> '' . new Mapping('/debug/login'),
			'logoutUrl'			=> '' . new Mapping('/logout'),
			'smartFoxSettings'	=> Encoding::indexOf($this->getWatena()->getMapping()->getHost(), '.com') ? 'ONLINE_TESTING' : ($_SERVER['COMPUTERNAME'] == 'JELLE-MONSTER' ? 'LOCALHOST' : 'GRINTERNAL'),
			'imageUrlPrefix'	=> '' . new Mapping('/files/toevla/festival'),
			'picasaUrlPrefix'	=> '' . new Mapping('/request/picasa'),
			'flickrUrlPrefix'	=> '' . new Mapping('/request/flickr'),
			'audioUrlPrefix'	=> '' . new Mapping('/files/toevla/audio')
		);
		$oStatement = DatabaseManager::getConnection('toevla')->select('game_config');
		foreach($oStatement as $aRow) {
			$aData[$aRow['name']] = $aRow['value'];
		}
		return $aData;
	}
}

?>