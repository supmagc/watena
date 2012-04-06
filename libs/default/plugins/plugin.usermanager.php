<?php
require_plugin('DatabaseManager');

class UserManager extends Plugin {
	
	const TYPE_GUEST = 0;
	const TYPE_MEMBER_LOOSE = 1;
	const TYPE_MEMBER_VERIFIED = 2;
	const TYPE_ADMIN = 3;
	const TYPE_TLA = 4;
	
	private static $s_oDatabaseConnection;
	private static $s_oLoggedInUser;
	
	public function init() {
	}
	
	public function make() {
		self::$s_oDatabaseConnection = DatabaseManager::getConnection($this->getConfig('DATABASECONNECTION', 'default'));
		if(isset($_SESSION['USERID'])) {
			self::$s_oLoggedInUser = $_SESSION['USERID'] ? new User($_SESSION['USERID']) : null;
		}
		else {
			$aProviders = array(new ProviderTwitter(), new ProviderFacebook());
		}
	}
	
	public static function getDatabaseConnection() {
		return self::$s_oDatabaseConnection;
	}
	
	public static function isNameAvailable($sName) {
		$oStatement = self::getDatabaseConnection()->select('user', $sName, 'name');
		return $oStatement->rowCount() === 0;
	}
	
	public static function create($sName, $nType) {
		if(self::isNameAvailable($sName)) {
			$nId = self::getDatabaseConnection()->insert('user', array(
				'type' => $nType,
				'name' => $sName
			));
			return new User($nId);
		}
		return false;
	}
	
	public static function loginByCredentials($sUsername, $sPassword) {
	
	}
	
	public static function loginByProvider(UserConnectionProvider $oConnectionProvider) {
		
	}
	
	public function isLoggedIn() {
		return self::$s_oLoggedInUser !== null;
	}
	
	public static function registerByProvider($sName, UserConnectionProvider $oConnectionProvider) {
		$oUser = self::create($sName, self::TYPE_MEMBER_LOOSE);
		$oUser->addConnection($oConnectionProvider);
		return $oUser;
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