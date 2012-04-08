<?php
require_plugin('DatabaseManager');

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
			self::$s_aConnectionProviders =  array('TWITTER' => new ProviderTwitter(), 'FACEBOOK' => new ProviderFacebook());
		}
		return self::$s_aConnectionProviders;
	}
	
	public static function getConnectionProvider($sName) {
		$aProviders = self::getConnectionProviders();
		return isset($aProviders[strtoupper($sName)]) ? $aProviders[strtoupper($sName)] : null;
	}
	
	public static function getLoggedInUser() {
		if(self::$s_oLoggedInUser === false) {
			if(isset($_SESSION['USERID'])) {
				self::$s_oLoggedInUser = $_SESSION['USERID'] ? new User($_SESSION['USERID']) : null;
			}
			else {
				$aProviders = self::getConnectionProviders();
				foreach($aProviders as $oProvider) {
					if($oProvider->isConnected()) {
						self::connectToProvider($oProvider);
						break;
					}
				}
			}			
		}
		return self::$s_oLoggedInUser;
	}
	
	public static function setLoggedInUser(User $oUser) {
		self::$s_oLoggedInUser = $oUser ? $oUser : null;
		$_SESSION['USERID'] = self::$s_oLoggedInUser->getId();
	}
	
	public static function isLoggedIn() {
		return self::getLoggedInUser() !== null;
	}
	
	public static function isNameAvailable($sName) {
		$oStatement = self::getDatabaseConnection()->select('user', $sName, 'name');
		return $oStatement->rowCount() === 0;
	}
	
	public static function isEmailAvailable($sEmail) {
		$oStatement = self::getDatabaseConnection()->select('user_email', $sEmail, 'email');
		return $oStatement->rowCount() === 0;
	}
	
	public static function create($sName, $nType) {
		// Shouldn't this be updated ?
		if(self::isNameAvailable($sName)) {
			$nId = self::getDatabaseConnection()->insert('user', array(
				'type' => $nType,
				'name' => $sName
			));
			return new User($nId);
		}
		return false;
	}
	
	public static function login($sUsername, $sPassword) {
		// NYI
	}
	
	public static function connectToProvider(UserConnectionProvider $oConnectionProvider) {
		if($oConnectionProvider->connect() && $oConnectionProvider->isConnected() && $oConnectionProvider->canBeConnectedTo(self::getLoggedInUser())) {
			$oUser = self::isLoggedIn() ? self::getLoggedInUser() : self::create($oConnectionProvider->getConnectionName(), self::TYPE_MEMBER_LOOSE);
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