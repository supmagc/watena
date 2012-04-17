<?php
require_plugin('UserManager');

class ToeVla extends Plugin {
	
	public static function getNewHash($sName = null) {
		$oUser = UserManager::getLoggedInUser();		
		$oConnection = DatabaseManager::getConnection('toevla');
		$nUserId = $oUser ? $oUser->getId() : null;
		$nCharacterId = null;		
		
		if($oUser) {
			$oConnection->delete('game_session', $oUser->getId(), 'userId');

			$oStatement = $oConnection->select('game_character', $oUser->getId(), 'userId');
			if($oStatement->rowCount() > 0) {
				$nCharacterId = $oStatement->fetchObject()->ID;
			}
			else {
				$nCharacterId = $oConnection->insert('game_character', array(
					'userId' => $oUser->getId(),
					'name' => $oUser->getName(),
					'data' => ''
				));				
			}
		}

		$nCount = 0;
		$sHash = null;
		do {
			++$nCount;
			$sHash = md5("TOEVLA.$nUserId.$nCharacterId.$nCount.".microtime(true));
		}
		while($oConnection->select('game_session', $sHash, 'hash')->rowCount() > 0);
		$oConnection->insert('game_session', array(
			'hash' => $sHash,
			'userId' => $nUserId,
			'characterId' => $nCharacterId
		));
		
		return $sHash;
	}
	
	/**
	* Retrieve version information of this plugin.
	* The format is an associative array as follows:
	* 'major' => Major version (int)
	* 'minor' => Minor version (int)
	* 'build' => Build version (int)
	* 'state' => Naming of the production state
	*/
	public function getVersion() {
		return array('major' => 0, 'minor' => 1, 'build' => 1, 'state' => 'dev');
	}
}

?>