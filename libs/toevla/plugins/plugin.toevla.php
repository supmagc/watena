<?php
require_plugin('UserManager');

class ToeVla extends Plugin {
	
	public static function getNewHash() {
		$oUser = UserManager::getLoggedInUser();		
		$oConnection = DatabaseManager::getConnection('toevla');
		
		if($oUser) {
			// Delete previous sessions
			$oConnection->delete('game_session', $oUser->getId(), 'userId');

			// Get existing characterid
			$oStatement = $oConnection->select('game_character', $oUser->getId(), 'userId');
			$nCharacterId = null;
			if($oStatement->rowCount() > 0) {
				$nCharacterId = $oStatement->fetchObject()->ID;
			}
			
			// Create valid hash and session
			$nCount = 0;
			$sHash = null;
			do {
				++$nCount;
				$sHash = md5("TOEVLA.{$oUser->getId()}.$nCharacterId.$nCount.".microtime(true));
			}
			while($oConnection->select('game_session', $sHash, 'hash')->rowCount() > 0);
			$oConnection->insert('game_session', array(
				'hash' => $sHash,
				'userId' => $oUser->getId(),
				'characterId' => $nCharacterId
			));

			// Return hash
			return $sHash;
		}
		return null;
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