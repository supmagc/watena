<?php
require_includeonce(dirname(__FILE__) . '/../usermanager/index.php');
require_plugin('DatabaseManager');
require_plugin('Socializer');

class UserManager extends Plugin {
	
	const TYPE_GUEST = 0;
	const TYPE_MEMBER_LOOSE = 1;
	const TYPE_MEMBER_VERIFIED = 2;
	const TYPE_ADMIN = 3;
	const TYPE_TLA = 4;
	
	private static $s_aConnectionProviders = null;
	private static $s_oDatabaseConnection = null;
	private static $s_oLoggedInUser = false;
	
	public function make() {
		self::$s_oDatabaseConnection = DatabaseManager::getConnection($this->getConfig('DATABASECONNECTION', 'default'));
	}
	
	public static function getDatabaseConnection() {
		return self::$s_oDatabaseConnection;
	}
	
	public static function getConnectionProviders() {
		if(!is_array(self::$s_aConnectionProviders)) {
			self::$s_aConnectionProviders =  array('FACEBOOK' => new ProviderFacebook());
		}
		return self::$s_aConnectionProviders;
	}
	
	public static function getConnectionProvider($sName) {
		$aProviders = self::getConnectionProviders();
		return isset($aProviders[strtoupper($sName)]) ? $aProviders[strtoupper($sName)] : null;
	}
	
	public static function getTwitterProvider() {
		return self::getConnectionProvider('TWITTER');
	}
	
	public static function getFacebookProvider() {
		return self::getConnectionProvider('FACEBOOK');
	}
	
	public static function getLoggedInUser() {
		if(self::$s_oLoggedInUser === false) {
			self::setLoggedInUser((isset($_SESSION['USERID']) && $_SESSION['USERID']) ? new User($_SESSION['USERID']) : null);
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
	
	public static function isNameAvailable($sName) {
		$oStatement = self::getDatabaseConnection()->select('user', $sName, 'name');
		return $oStatement->rowCount() === 0;
	}
	
	public static function isEmailAvailable($sEmail) {
		$oStatement = self::getDatabaseConnection()->select('user_email', $sEmail, 'email');
		return $oStatement->rowCount() === 0;
	}
	
	public static function createUser($sName, $nType) {
		if(self::isNameAvailable($sName)) {
			$nId = self::getDatabaseConnection()->insert('user', array(
				'type' => $nType,
				'name' => $sName
			));
			return new User($nId);
		}
		else {
			throw new UserDuplicateNameException();
		}
	}
	
	public static function login($sUsername, $sPassword) {
		// NYI
	}
	
	public static function connectToProvider(UserConnectionProvider $oConnectionProvider) {
		if($oConnectionProvider->connect() && $oConnectionProvider->isConnected() && $oConnectionProvider->canBeConnectedTo(self::getLoggedInUser())) {
			$oUser = self::isLoggedIn() ? self::getLoggedInUser() : self::createUser($oConnectionProvider->getConnectionName(), self::TYPE_MEMBER_LOOSE);
			$oUser->addConnection($oConnectionProvider);
			$oConnectionProvider->update($oUser);
			self::setLoggedInUser($oUser);
			return $oUser;
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