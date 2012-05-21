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
	
	public static function parseFlickr($sData, &$bError) {
		if($sData) {
			$aData = array();
			$aMatches = array();
			if(Encoding::regFind('/photos/([0-9@N]+)/sets/([0-9]+)', $sData, $aMatches))
				$aData = array('source' => $sData, 'type' => 'set', 'url' => "set=$aMatches[2]&nsid=$aMatches[1]");
			else if(Encoding::regFind('/photos/([0-9@N]+)/favorites', $sData, $aMatches))
				$aData = array('source' => $sData, 'type' => 'faves', 'url' => "nsid=$aMatches[1]");
			else if(Encoding::regFind('/photos/([0-9@N]+)', $sData, $aMatches))
				$aData = array('source' => $sData, 'type' => 'public', 'url' => "id=$aMatches[1]");
			else if(Encoding::regFind('/photoset\\.gne\\?(set=[0-9]+&nsid=[0-9@N]+)', $sData, $aMatches))
				$aData = array('source' => $sData, 'type' => 'set', 'url' => $aMatches[1]);
			else if(Encoding::regFind('/photos_faves\\.gne\\?(nsid=[0-9@N]+)', $sData, $aMatches))
				$aData = array('source' => $sData, 'type' => 'faves', 'url' => $aMatches[1]);
			else if(Encoding::regFind('/photos_public\\.gne\\?(id=[0-9@N]+)', $sData, $aMatches))
				$aData = array('source' => $sData, 'type' => 'public', 'url' => $aMatches[1]);
			else {
				$aData = array('source' => $sData, 'type' => 'unknown', 'url' => '');
				$bError = true;
			}
			return serialize($aData);
		}
	}
	
	public static function parsePicasa($sData, &$bError) {
		if($sData) {
			$aData = array();
			$aMatches = array();
			if(Encoding::regFind('(user/[0-9]+/albumid/[0-9]+)', $sData, $aMatches))
				$aData = array('source' => $sData, 'type' => 'default', 'url' => $aMatches[1]);
			else if(Encoding::regFind('(user/[0-9]+)', $sData, $aMatches))
				$aData = array('source' => $sData, 'type' => 'default', 'url' => $aMatches[1]);
			else if(Encoding::regFind('google\\.com/([0-9]+)/photos/([0-9]+)', $sData, $aMatches))
				$aData = array('source' => $sData, 'type' => 'default', 'url' => "user/$aMatches[1]/albumid/$aMatches[2]");
			else if(Encoding::regFind('google\\.com/([0-9]+)', $sData, $aMatches))
				$aData = array('source' => $sData, 'type' => 'default', 'url' => "user/$aMatches[1]");
			else {
				$aData = array('source' => $sData, 'type' => 'unknown', 'url' => '');
				$bError = true;
			}
			return serialize($aData);
		}
	}
	
	public static function parseFacebook($sData, &$bError) {
		if($sData) {
			$aMatches = array();
			if(Encoding::regFind('([-a-zA-Z_.0-9]+)/?$', Encoding::trim($sData), $aMatches))
				$sData = $aMatches[1];
			else if(Encoding::regFind('(/|id=)([0-9]+)$', Encoding::trim($sData), $aMatches))
				$sData = $aMatches[2];
			else {
				$bError = true;
				$sData = null;
			}
			return $sData;
		}
	}
	
	public static function parseTwitterName($sData, &$bError) {
		if($sData) {
			$aMatches = array();
			if(Encoding::regFind('^@?([a-zA-Z0-9_]+)$', Encoding::trim($sData), $aMatches))
				$sData = $aMatches[1];
			else if(Encoding::regFind('twitter\\.com/(#!/)?([a-zA-Z0-9_]+)$', Encoding::trim($sData), $aMatches))
				$sData = $aMatches[2];
			else {
				$bError = true;
				$sData = null;
			}
			return $sData;
		}
	}
	
	public static function parseTwitterHash($sData, &$bError) {
		if($sData) {
			$aMatches = array();
			if(Encoding::regFind('^#?([a-zA-Z0-9_]+)$', Encoding::trim($sData), $aMatches))
				$sData = $aMatches[1];
			else {
				$bError = true;
				$sData = null;
			}
			return $sData;
		}
	}
	
	public static function parseYoutube($sData, &$bError) {
		if($sData) {
			$aMatches = array();
			if(Encoding::regFind('youtu\\.be/([-a-zA-Z0-9]+)', $sData, $aMatches))
				$sData = $aMatches[1];
			else if(Encoding::regFind('youtube\\.com/watch\\?.*v=([-a-zA-Z0-9]+)', $sData, $aMatches))
				$sData= $aMatches[1];
			else if(Encoding::regFind('youtube-nocookie\\.com/embed/([-a-zA-Z0-9]+)', $sData, $aMatches))
				$sData = $aMatches[1];
			else {
				$bError = true;
				$sData = null;
			}
			return !$bError ? "http://www.youtube-nocookie.com/embed/$sData?version=3&feature=player_embedded&autoplay=1&controls=0&rel=0&showinfo=0" : $sData;
		}
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