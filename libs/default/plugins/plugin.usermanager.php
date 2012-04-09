<?php
require_plugin('Socializer');
require_plugin('DatabaseManager');
require_includeonce(dirname(__FILE__) . '/../usermanager/index.php');

class UserManager extends Plugin {
	
	const TYPE_GUEST = 0;
	const TYPE_CONNECTION = 1;
	const TYPE_MEMBER = 2;
	const TYPE_ADMIN = 3;
	const TYPE_TLA = 4;
	
	private static $s_aConnectionProviders = null;
	private static $s_oDatabaseConnection = null;
	private static $s_oLoggedInUser = false;
	
	public function init() {
		self::$s_oDatabaseConnection = DatabaseManager::getConnection($this->getConfig('DATABASECONNECTION', 'default'));
		self::$s_aConnectionProviders = array();
		if($this->getConfig('PROVIDERFACEBOOK_ENABLED', false))
			self::$s_aConnectionProviders['ProviderFacebook'] = new ProviderFacebook();
		if($this->getConfig('PROVIDERTWITTER_ENABLED', false))
			self::$s_aConnectionProviders['ProviderTwitter'] = new ProviderTwitter();
	}
	
	public static function getDatabaseConnection() {
		return self::$s_oDatabaseConnection;
	}
	
	public static function getConnectionProviders() {
		return self::$s_aConnectionProviders;
	}
	
	public static function getConnectionProvider($sName) {
		$aProviders = self::getConnectionProviders();
		return isset($aProviders[strtoupper($sName)]) ? $aProviders[strtoupper($sName)] : null;
	}
	
	public static function getTwitterProvider() {
		return self::getConnectionProvider('ProviderTwitter');
	}
	
	public static function getFacebookProvider() {
		return self::getConnectionProvider('ProviderFacebook');
	}
	
	public static function getLoggedInUser() {
		if(self::$s_oLoggedInUser === false) {
			self::setLoggedInUser((isset($_SESSION['USERID']) && $_SESSION['USERID']) ? User::load($_SESSION['USERID']) : null);
		}
		return self::$s_oLoggedInUser;
	}
	
	public static function setLoggedInUser(User $oUser = null) {
		self::$s_oLoggedInUser = $oUser ? $oUser : null;
		$_SESSION['USERID'] = $oUser ? $oUser->getId() : 0;
	}
	
	public static function isLoggedIn() {
		return (bool)self::getLoggedInUser();
	}
	
	public static function getUserIdByName($sName) {
		$oStatement = self::getDatabaseConnection()->select('user', $sName, 'name');
		return $oStatement->rowCount() > 0 ? $oStatement->fetchObject()->ID : false;
	}
	
	public static function getUserIdByEmail($sEmail) {
		$oStatement = self::getDatabaseConnection()->select('user_email', $sEmail, 'email');
		return $oStatement->rowCount() > 0 ? $oStatement->fetchObject()->userId : false;
	}
	
	public static function getUserIdByConnection(UserConnectionProvider $oConnectionProvider) {
		$oStatement = self::getDatabaseConnection()->select('user_connection', array($oConnectionProvider->getConnectionId(), $oConnectionProvider->getName()), array('connectionId', 'provider'), 'AND');
		return $oStatement->rowCount() > 0 ? $oStatement->fetchObject()->userId : false;
	}
		
	public static function connectToProvider(UserConnectionProvider $oConnectionProvider) {
		if($oConnectionProvider->connect()) {
			$nConnectionUserId = self::getUserIdByConnection($oConnectionProvider);
			$oConnectionUser = $nConnectionUserId !== false ? User::load($nConnectionUserId) : null;
			$oCurrentUser = self::getLoggedInUser() ? self::getLoggedInUser() : null;
			if($oConnectionUser && $oCurrentUser && $oConnectionProvider->canBeConnectedTo($oCurrentUser)) {
				if($oConnectionUser != $oCurrentUser)
					throw new UserDuplicateLoginException();
			}
			else if($oConnectionUser && $oConnectionProvider->canBeConnectedTo($oConnectionUser)) {
				$oCurrentUser = $oConnectionUser;
			}
			else if($oCurrentUser && $oConnectionProvider->canBeConnectedTo($oCurrentUser)) {
				$oCurrentUser->addConnection($oConnectionProvider);
			}
			else {
				$oCurrentUser = User::create($oConnectionProvider->getConnectionName(), self::TYPE_CONNECTION);
				$oCurrentUser->addConnection($oConnectionProvider);
			}
			self::setLoggedInUser($oCurrentUser);
			$oConnectionProvider->update($oCurrentUser);
			return self::getLoggedInUser();
		}
		return false;
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