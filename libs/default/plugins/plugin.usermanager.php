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
	
	private $m_aConnectionProviders = null;
	private $m_oDatabaseConnection = null;
	private static $s_oLoggedInUser = false;
	private static $s_oSingleton;
	
	public function make() {
		$this->m_oDatabaseConnection = DatabaseManager::getConnection($this->getConfig('DATABASECONNECTION', 'default'));
		$this->m_aConnectionProviders = array();
		if($this->getConfig('PROVIDERFACEBOOK_ENABLED', false))
			$this->m_aConnectionProviders['PROVIDERFACEBOOK'] = new ProviderFacebook();
		if($this->getConfig('PROVIDERTWITTER_ENABLED', false))
			$this->m_aConnectionProviders['PROVIDERTWITTER'] = new ProviderTwitter();
	}
	
	public function init() {
		self::$s_oSingleton = $this;
	}
	
	public static function getDatabaseConnection() {
		return self::$s_oSingleton->m_oDatabaseConnection;
	}
	
	public static function getConnectionProviders() {
		return self::$s_oSingleton->m_aConnectionProviders;
	}
	
	public static function getConnectionProvider($sName) {
		$aProviders = self::getConnectionProviders();
		return isset($aProviders[strtoupper($sName)]) ? $aProviders[strtoupper($sName)] : null;
	}

	/**
	 * @return ProviderTwitter
	 */
	public static function getProviderTwitter() {
		return self::getConnectionProvider('ProviderTwitter');
	}
	
	/**
	 * @return ProviderFacebook
	 */
	public static function getProviderFacebook() {
		return self::getConnectionProvider('ProviderFacebook');
	}
	
	public static function getLoggedInUser() {
		if(self::$s_oLoggedInUser === false && isset($_SESSION['USERID']) && $_SESSION['USERID']) {
			self::$s_oLoggedInUser = User::load($_SESSION['USERID']);
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
	
	public static function isValidName($sName) {
		return Encoding::regMatch('^[-a-zA-z0-9._ ]{3,64}$', $sName);
	}
	
	public static function isValidEmail($sEmail) {
		return is_email($sEmail);
	}
	
	/**
	 * Make sure the current connection is fully established and create/merge a user.
	 * A lot of things can go wrong. If a connection is not established,
	 * the method will return false.
	 * If a connection is made but for some logical reason a user cannot be linked/created
	 * an exception will be thrown containing  the relevant data.
	 * 
	 * @param UserConnectionProvider $oConnectionProvider
	 * @param string $sName Possebility to overwrite the username.
	 * @throws UserDuplicateLoginException The connection is known and there is a logged in user, but they don't match.
	 * @throws UserDuplicateEmailException The email-adress is known and there is a logged in user, but they don't match.
	 * @throws UserUnverifiedEmailException The connection can be linked to an email-adress, but the adress is not yet verified.
	 * @throws UserInvalidNameException The name is invalid, and should be overwritten.
	 * @throws UserDuplicateNameException A user with the same name allready exists, the name should be overwritten.
	 * @return User|boolean
	 */
	public static function connectToProvider(UserConnectionProvider $oConnectionProvider, $sName = null) {
		// Make sure we are connected
		if($oConnectionProvider->connect() || $oConnectionProvider->isConnected()) {
			
			// Retrieve a connection-user if available
			$nConnectionUserId = self::getUserIdByConnection($oConnectionProvider);
			$oConnectionUser = $nConnectionUserId !== false ? User::load($nConnectionUserId) : null;
			
			// Retrieve a current-user if available
			$oCurrentUser = self::getLoggedInUser() ? self::getLoggedInUser() : null;
			
			// If we have a current user and a connection user
			if($oConnectionUser && $oCurrentUser) {
				// If both are the same, all is good
				// If both are different, we don't know what to do
				if($oConnectionUser != $oCurrentUser)
					throw new UserDuplicateLoginException($oCurrentUser, $oConnectionUser);
			}
			
			// If we only have a connection user
			else if($oConnectionUser) {
				// set this user to be the current user
				$oCurrentUser = $oConnectionUser;
			}
			
			// If we only have a current logged in user
			else if($oCurrentUser) {
				// Make sure not to mix up the email-adresses
				$sConnectionEmail = $oConnectionProvider->getConnectionEmail();
				if(self::isValidEmail($sConnectionEmail) && ($nId = self::getUserIdByEmail($sConnectionEmail)) !== false && $nId != $oCurrentUser->getId())
					throw new UserDuplicateEmailException($sConnectionEmail);

				// If all is good, add the connection
				$oCurrentUser->addConnection($oConnectionProvider);
			}
			
			// If the connection is unknown and no logged in user
			else {				
				// If an email-adress is found that allready exists, join the connection on that user
				$sConnectionEmail = $oConnectionProvider->getConnectionEmail();
				if(self::isValidEmail($sConnectionEmail) && ($nId = self::getUserIdByEmail($sConnectionEmail)) !== false) {
					$oCurrentUser = User::load($nId);
					if(!$oCurrentUser->getEmail($sConnectionEmail)->getVerified())
						throw new UserUnverifiedEmailException($sConnectionEmail);
				}
				else {
					// Make sure we don't create users with false names
					$sConnectionName = $sName ?: $oConnectionProvider->getConnectionName();
					if(!self::isValidName($sConnectionName))
						throw new UserInvalidNameException($sConnectionName);
	
					// Make sure we don't create a user with a duplicate name
					if(self::getUserIdByName($sConnectionName) !== false) 
						throw new UserDuplicateNameException($sConnectionName);
					
					$oCurrentUser = User::create($sConnectionName, self::TYPE_CONNECTION);
				}
				$oCurrentUser->addConnection($oConnectionProvider);
			}
			
			// Update the userdata
			$oConnectionProvider->update($oCurrentUser);
			$oCurrentUser->addEmail($oConnectionProvider->getConnectionEmail(), true);
			$oCurrentUser->getConnection($oConnectionProvider)->setConnectionData($oConnectionProvider->getConnectionData());
			$oCurrentUser->getConnection($oConnectionProvider)->setConnectionTokens($oConnectionProvider->getConnectionTokens());
				
			// Set the user as logged in
			self::setLoggedInUser($oCurrentUser);
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