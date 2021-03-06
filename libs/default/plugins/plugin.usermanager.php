<?php
require_plugin('Socializer');
require_plugin('DatabaseManager');
require_includeonce(dirname(__FILE__) . '/../usermanager/usermanager.php');

class UserManager extends Plugin {

	const PERMISSION_UNDEFINED = 0;
	const PERMISSION_GRANTED = 1;
	const PERMISSION_REVOKED = 2;
	
	private $m_aConnectionProviders = null;
	private $m_oDatabaseConnection = null;
	private static $s_oActiveSession = false;
	private static $s_oSingleton;
	
	public function make(array $aMembers) {
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

	public static function setDatabaseConnection(DbConnection $oConnection) {
		self::$s_oSingleton->m_oDatabaseConnection = $oConnection;
	}
	
	/**
	 * @return DbConnection
	 */
	public static function getDatabaseConnection() {
		return self::$s_oSingleton->m_oDatabaseConnection;
	}
	
	public static function getPasswordFormat() {
		return self::$s_oSingleton->getConfig('PASSWORD_FORMAT', '^.{6,}$');
	}
	
	/**
	 * @return array
	 */
	public static function getConnectionProviders() {
		return self::$s_oSingleton->m_aConnectionProviders;
	}
	
	/**
	 * @param string $sName
	 * @return ConnectionProvider
	 */
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
	
	/**
	 * @param string $sName
	 * @return int|false
	 */	
	public static function getUserIdByName($sName) {
		$oStatement = self::getDatabaseConnection()->select('user', $sName, 'name');
		return $oStatement->rowCount() > 0 ? $oStatement->fetchObject()->ID : false;
	}
	
	/**
	 * @param string $sEmail
	 * @return int|false
	 */	
	public static function getUserIdByEmail($sEmail) {
		$oStatement = self::getDatabaseConnection()->select('user_email', $sEmail, 'email');
		return $oStatement->rowCount() > 0 ? $oStatement->fetchObject()->userId : false;
	}

	/**
	 * @param string $sToken
	 * @return int|false
	 */
	public static function getUserIdByToken($sToken) {
		$oStatement = self::getDatabaseConnection()->select('user_session', $sToken, 'token');
		return $oStatement->rowCount() > 0 ? $oStatement->fetchObject()->userId : false;
	}
	
	/**
	 * @param UserConnectionProvider $oConnectionProvider
	 * @return int|false
	 */	
	public static function getUserIdByConnection(UserConnectionProvider $oConnectionProvider) {
		$oStatement = self::getDatabaseConnection()->select('user_connection', array($oConnectionProvider->getConnectionId(), $oConnectionProvider->getName()), array('connectionId', 'provider'), 'AND');
		return $oStatement->rowCount() > 0 ? $oStatement->fetchObject()->userId : false;
	}
	
	public static function isLoggedIn() {
		return (bool)self::getLoggedInUser();
	}
	
	public static function isValidName($sName) {
		return Encoding::regMatch('^[-a-zA-z0-9.@_ ]{3,64}$', ''.$sName);
	}
	
	public static function isValidEmail($sEmail) {
		return is_email($sEmail) && Encoding::length($sEmail) <= 128;
	}
	
	public static function isValidPassword($sPassword) {
		return Encoding::regMatch(self::getPasswordFormat(), ''.$sPassword);
	}
	
	public static function isValidPermission($nPermission) {
		return $nPermission >= 0 && $nPermission <= 2;
	}
	
	public static function isValidToken($sToken) {
		return Encoding::regMatch('^[a-z0-9]{16}$', ''.$sToken);
	}
	
	public static function getTableUser() {
		return self::$s_oSingleton->m_oDatabaseConnection->getTable('user');
	}

	public static function getTableUserEmail() {
		return self::$s_oSingleton->m_oDatabaseConnection->getTable('user_email');
	}

	public static function getTableUserSession() {
		return self::$s_oSingleton->m_oDatabaseConnection->getMultiTable('user_session', array('userId', 'token'));
	}
	
	/**
	 * Try to login a user by the given name (and password)
	 * This returns the user when login.
	 * 
	 * @param string $sName
	 * @param string $sPassword
	 * @throws UserInvalidNameException The provided name is of an invalid format.
	 * @throws UserUnknownNameException The provided name is not known for any user.
	 * @throws UserUnverifiedUserException The user exists, but isn't verified.
	 * @throws UserNoPasswordException The user exists, but has no password set.
	 * @throws UserInvalidPasswordException The given password is invalid.
	 * @return User
	 */
	public static function loginByName($sName, $sPassword) {
		// Check the validity of the email
		if(!UserManager::isValidName($sName))
			throw new UserInvalidNameException($sName);
		
		// Get and check the matching UserId
		$oUser = User::Load(self::getUserIdByName($sName));
		if($oUser === false) 
			throw new UserUnknownNameException($sName);

		// Try to log the user in with the provided password
		return self::Login($oUser, $sPassword);
	}
	
	/**
	 * Try to login a user by the given email (and password)
	 * This returns the user when login.
	 * 
	 * @param string $sEmail
	 * @param string $sPassword
	 * @throws UserInvalidEmailException The provided email is of an invalid format.
	 * @throws UserUnknownEmailException The provided email is not known for any user.
	 * @throws UserUnverifiedEmailException The email is known, but not verified. It thus cannot be used to login.
	 * @throws UserUnverifiedUserException The user exists, but isn't verified.
	 * @throws UserNoPasswordException The user exists, but has no password set.
	 * @throws UserInvalidPasswordException The given password is invalid.
	 * @return User
	 */
	public static function loginByEmail($sEmail, $sPassword) {
		// Check the validity of the email
		if(!UserManager::isValidEmail($sEmail))
			throw new UserInvalidEmailException($sEmail);
		
		// Get and check the matching UserId
		$oUser = User::Load(self::getUserIdByEmail($sEmail));
		if($oUser === null) 
			throw new UserUnknownEmailException($sEmail);
		
		// Get the user and the email-object
		$oEmail = $oUser->getEmail($sEmail);

		// Verify the email-object
		if(!$oEmail->isVerified()) 
			throw new UserUnverifiedEmailException($oEmail);
		
		// Try to log the user in with the provided password
		return self::Login($oUser, $sPassword);
	}
	
	public static function loginBySession($sSession) {
		
	}
	
	public static function loginByToken($sToken) {
		
		$nUserId = self::getUserIdByToken($sToken);
		// Check the validity of the token
		if(!UserManager::isValidToken($sToken))
			throw new UserTokenInvalidException($sToken);
		
		$oUser = User::load(self::getUserIdByToken($sToken));
		
		return self::login($oUser, $sPassword);
	}
	
	/**
	 * Try to login the specified user with the given password.
	 * Possibly the user won't have any password set ... see exception.
	 * This returns the user when login.
	 * 
	 * @param User $oUser
	 * @param string $sPassword
	 * @throws UserUnverifiedUserException The user exists, but isn't verified.
	 * @throws UserNoPasswordException The user exists, but has no password set.
	 * @throws UserInvalidPasswordException The given password is invalid.
	 * @return User
	 */
	public static function login(User $oUser, $sPassword) {
		// Check if verified
		if(!$oUser->isVerified())
			throw new UserUnverifiedUserException($oUser);
		
		// Check if user has a password
		if(!$oUser->hasPassword())
			throw new UserNoPasswordException($oUser);
		
		// Verify the given password
		if(!UserManager::isValidPassword($sPassword) || !$oUser->verifyPassword($sPassword))
			throw new UserInvalidPasswordException($sPassword);

		// Create a valid session
		self::setLoggedInUser($oUser);
		
		// Retrieve the user
		return $oUser;
	}

	/**
	 * Logout the user active in the current session.
	 */
	public static function logout() {
		self::setLoggedInUser(null);
	}
	
	/**
	 * Register a new user
	 * 
	 * @param string $sName
	 * @param string $sPassword
	 * @param string $sEmail
	 * @throws UserInvalidNameException
	 * @throws UserDuplicateNameException
	 * @throws UserInvalidPasswordException
	 * @throws UserInvalidEmailException
	 * @throws UserDuplicateEmailException
	 * @return User
	 */
	public static function register($sName, $sPassword = null, $sEmail = null) {
		if(!UserManager::isValidName($sName))
			throw new UserInvalidNameException($sName);
		if(UserManager::getUserIdByName($sName))
			throw new UserDuplicateNameException($sName);
		if($sPassword && !UserManager::isValidPassword($sPassword))
			throw new UserInvalidPasswordException($sPassword);
		if($sEmail && !UserManager::isValidEmail($sEmail))
			throw new UserInvalidEmailException($sEmail);
		if($sEmail && UserManager::getUserIdByEmail($sEmail))
			throw new UserDuplicateEmailException($sEmail);
		
		$oUser = User::create($sName);
		if($sPassword) $oUser->setPassword($sPassword);
		if($sEmail) $oUser->addEmail($sEmail);
		return $oUser;
	}
	
	/**
	 * Set the given user as currently logged in for the active session.
	 * If the given instance if null, this will invalidate the logged in user.
	 * 
	 * @param User $oUser
	 */
	public static function setLoggedInUser(User $oUser = null, $bRemember = false) {
		if($oUser) {
			$oSession = $oUser->createSession(Request::ip(), Request::useragent());
	
			$sSessionKey = $oUser->getId() . '.' . $oSession->getToken();
			$sSessionKeyHash = md5($sSessionKey . '.' . $oUser->getHash());
			$sSessionKey .= '.' . $sSessionKeyHash;
			
			self::$s_oActiveSession = $oSession;
			$_SESSION['USER'] = $sSessionKey;
			if($bRemember) {
				Cookies::save('USER', $sSessionKey);
			}
		}
		else {
			self::$s_oActiveSession = null;
			unset($_SESSION['USER']);
			Cookies::reset('USER');
		}
	}
	
	/**
	 * Return the currently active user-session.
	 * The first time, this function will look for a valid user-session-key
	 * within $_SESSION and $_COOKIE (in that order).
	 * 
	 * @return UserSession|null
	 */
	public static function getActiveSession() {
		if(self::$s_oActiveSession === false) {
			$sSessionKey = '';
			if(isset($_SESSION['USER'])) {
				$sSessionKey = $_SESSION['USER'];
			}
			else if(isset($_COOKIE['USER'])) {
				$sSessionKey = $_COOKIE['USER'];
			}
				
			self::$s_oActiveSession = null;
			if($sSessionKey) {
				$aSessionParts = explode('.', $sSessionKey);
				$nUserId = $aSessionParts[0];
				$sToken = $aSessionParts[1];
				$sHash = $aSessionParts[2];
		
				$oUser = User::load($nUserId);
				if($oUser && md5($nUserId . '.' . $sToken . '.' . $oUser->getHash()) == $sHash) {
					$oSession = UserSession::load($oUser, $sToken);
					if($oSession) {
						$oSession->setIp(Request::ip());
						$oSession->setUseragent(Request::useragent());
						$oSession->setActivity(Time::getUtcTime());
						self::$s_oActiveSession = $oSession;
					}
				}
			}
		}
		return self::$s_oActiveSession;
		
	}

	/**
	 * Return the user linked to the currently active session.
	 * 
	 * @see getActiveSession()
	 * @return User|null
	 */
	public static function getLoggedInUser() {
		$oSession = self::getActiveSession();
		return $oSession ? $oSession->getUser() : null;
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
	 * @throws UserDuplicateUserException The connection is known and there is a logged in user, but they don't match.
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
					throw new UserDuplicateUserException($oCurrentUser, $oConnectionUser);
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
					if(!$oCurrentUser->getEmail($sConnectionEmail)->isVerified())
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
					
					$oCurrentUser = User::create($sConnectionName);
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