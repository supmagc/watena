<?php
/**
 * Class reepresenting a database User.
 * 
 * @author Jelle Voet
 * @version 0.2.0
 */
class User extends UserManagerVerifiable {
	
	private $m_aConnections = false;
	private $m_aSessions = false;
	private $m_aEmails = false;
	
	private $m_oContainerMails;
	
	public function getContainerMails() {
		if(!$this->m_oContainerMails) {
			$this->m_oContainerMails = new Container();
			$oStatement = UserManager::getDatabaseConnection()->select('user_email', $this->getId(), 'userId');
			$aData = UserEmail::loadObjectList(UserManager::getTableUserEmail(), $oStatement);
			foreach($aData as $oEmail) {
				$this->m_oContainerMails->addItem($oEmail);
			}
		}
		
		return $this->m_oContainerMails;
	}
	
	/**
	 * Check if a password is set for this user.
	 * 
	 * @return boolean
	 */
	public function hasPassword() {
		return (bool)$this->getDataValue('password');
	}

	/**
	 * Get the gender of the user.
	 * This value is optionel.
	 * Possible values: male, female
	 * 
	 * @return null|string
	 */
	public function getGender() {
		return $this->getDataValue('gender');
	}

	/**
	 * Get the (user-)name of the user.
	 * This value will always be set, and needs to be unique for all users.
	 * 
	 * @return string
	 */
	public function getName() {
		return $this->getDataValue('name');
	}
	
	/**
	 * Get the birthday of the user.
	 * This value is optional.
	 * The value will be SQL formatted (MySQL: yyyy-mm-dd)
	 * 
	 * @return null|string
	 */
	public function getBirthday() {
		return $this->getDataValue('birthday');
	}

	/**
	 * Get the firstname of the user.
	 * This value is optional.
	 * 
	 * @return null|string (maxlength: 64)
	 */
	public function getFirstname() {
		return $this->getDataValue('firstname');
	}

	/**
	 * Get the lastname of the user.
	 * This value is optional.
	 * 
	 * @return null|string (maxlength: 64)
	 */
	public function getLastname() {
		return $this->getDataValue('lastname');		
	}
	
	/**
	 * Get the timezone of the user.
	 * This value is optional.
	 * The value is supposed to be a valid php timezone, of which you can create a Time-object.
	 * 
	 * @return Time::isValidTimezone()
	 * @return null|string
	 */
	public function getTimezone() {
		return $this->getDataValue('timezone');
	}
	
	/**
	 * Get the locale of the user.
	 * This value is optional.
	 * ex: En, EN, En_Uk
	 * 
	 * @return null|string
	 */
	public function getLocale() {
		return $this->getDataValue('locale');
	}
	
	/**
	 * Get the hash of the user.
	 * This key should be be kept private, and used to encrypt any user-data publicly exposed.
	 * 
	 * @return string (length: 32)
	 */
	public function getHash() {
		return $this->getDataValue('hash');
	}
	
	/**
	 * Check if the user is considered top-level-admin.
	 * This is a flag that should be used to overwrite any other permissions.
	 * 
	 * @return boolean
	 */
	public function isTla() {
		return (bool)$this->getDataValue('tla', false);
	}
	
	/**
	 * Set the gender for the user.
	 * Valid values are m, f, male, female, null
	 * 
	 * @param mixed $mValue
	 * @return boolean
	 */
	public function setGender($mValue) {
		$mValue = Encoding::toLower($mValue);
		if(empty($mValue)) $mValue = null;
		if($mValue === 'm') $mValue = 'male';
		if($mValue === 'f') $mValue = 'female';
		if(in_array($mValue, array('male', 'female', null))) {
			return $this->setDataValue('gender', $mValue);
		}
		return false;
	}
	
	/**
	 * Set the (user-)name for the user.
	 * The name shoumd be unique, and conform according to UserManager::isValidName().
	 * 
	 * @see UserManager::isValidName()
	 * @param string $sValue
	 * @return boolean
	 */
	public function setName($sValue) {
		$nUserId = UserManager::getUserIdByName($sValue);
		if(UserManager::isValidName($sValue) && (!$nUserId || $nUserId == $this->getId())) {
			return $this->setDataValue('name', $sValue);
		}
		return false;
	}
	
	/**
	 * Set the birthday for the user.
	 * Valid values are yyyy-mm-dd, dd-mm-yyyy, yyyy/mm/dd, dd/mm/yyyy, null  
	 * 
	 * @param mixed $mValue
	 * @return boolean
	 */
	public function setBirthday($mValue) {
		$aMatches = array();
		if(empty($mValue)) {
			return $this->setDataValue('birthday', null);
		}
		if(Encoding::regFind('^([0-9]{4})[-/]([0-9]{1,2})[-/]([0-9]{1,2})$', '' . $mValue, $aMatches) && checkdate($aMatches[2], $aMatches[3], $aMatches[1])) {
			return $this->setDataValue('birthday', "$aMatches[1]-$aMatches[2]-$aMatches[3]");
		}
		if(Encoding::regFind('^([0-9]{1,2})[-/]([0-9]{1,2})[-/]([0-9]{4})$', '' . $mValue, $aMatches) && checkdate($aMatches[2], $aMatches[1], $aMatches[3])) {
			return $this->setDataValue('birthday', "$aMatches[3]-$aMatches[2]-$aMatches[1]");
		}
		return false;
	}
	
	/**
	 * Set the firstname of the user.
	 * The value will be trimmed, and capped at 64 characters.
	 * This value is optional.
	 * 
	 * @param string|null $mValue
	 * @return boolean
	 */
	public function setFirstname($mValue) {
		$mValue = Encoding::trim($mValue);
		$mValue = Encoding::substring($mValue, 0, 64);
		$mValue = Encoding::trim($mValue);
		if(empty($mValue)) $mValue = null;
		return $this->setDataValue('firstname', $mValue);
	}
		
	/**
	 * Set the lastname of the user.
	 * The value will be trimmed, and capped at 64 characters.
	 * This value is optional.
	 * 
	 * @param string|null $mValue
	 * @return boolean
	 */
	public function setLastname($mValue) {
		$mValue = Encoding::trim($mValue);
		$mValue = Encoding::substring($mValue, 0, 64);
		$mValue = Encoding::trim($mValue);
		if(empty($mValue)) $mValue = null;
		return $this->setDataValue('lastname', $mValue);
	}
	
	/**
	 * Set the timezone for the user.
	 * The input wille be formatted by Time::formatTimezone() and verified by Time::isValidTimezone().
	 * This value is optional
	 * 
	 * @see Time::formatTimezone()
	 * @see Time::isValidTimezone()
	 * @param string|null $mValue
	 * @return boolean
	 */
	public function setTimezone($mValue) {
		$mValue = empty($mValue) ? null : Time::formatTimezone($mValue);
		if(!$mValue || Time::isValidTimezone($mValue)) {
			return $this->setDataValue('timezone', $mValue);
		}
		return false;
	}
	
	/**
	 * The the locale for the user.
	 * Short and long locales are supported (nl, be_nl).
	 * This value is optional
	 * 
	 * @param string|null $mValue
	 * @return boolean
	 */
	public function setLocale($mValue) {
		if(empty($mValue)) $mValue = null;
		if(!$mValue || Encoding::regMatch('^[a-z]{2}(_[a-z]{2})?$', '' . $mValue, 'i')) {
			return $this->setDataValue('locale', $mValue);
		}
		return false;
	}

	/**
	 * Set the password for the user.
	 * The input will be validated by UserManager::isValidPassword().
	 * If valid, the password will be encoded before being saved.
	 * This value is optional.
	 * 
	 * @see User::encodePassword()
	 * @see UserManager::isValidPassword()
	 * @param string|null $mValue
	 * @return boolean
	 */
	public function setPassword($mValue) {
		if(empty($mValue)) $mValue == null;
		if(!$mValue || UserManager::isValidPassword($mValue)) {
			return $this->setDataValue('password', $this->encodePassword($mValue));
		}
		return false;
	}
	
	/**
	 * Verify if the given password matches the one registered for the user.
	 * If no password is set, this will always return false.
	 * 
	 * @see User::encodePassword()
	 * @param string $mValue
	 * @return boolean
	 */
	public function verifyPassword($mValue) {
		return !empty($mValue) && $this->getDataValue('password') === $this->encodePassword($mValue);
	}
	
	public function getConnections() {
		if($this->m_aConnections === null) {
			$this->m_aConnections = array();
			$oStatement = UserManager::getDatabaseConnection()->select('user_connection', $this->getId(), 'userId');
			foreach($oStatement as $aData) {
				$oConnection = UserConnection::load($aData);
				$this->m_aConnections[$oConnection->getProvider()] = $oConnection;
			}
		}
		return $this->m_aConnections;
	}
	
	public function addConnection(UserConnectionProvider $oConnectionProvider) {
		if($oConnection = UserConnection::create($this, $oConnectionProvider)) {
			$this->m_aConnections[$oConnection->getProvider()] = $oConnection;
		}
		return $this->getConnection($oConnectionProvider);
	}
	
	public function getConnection(UserConnectionProvider $oConnectionProvider) {
		$aConnections = $this->getConnections();
		return isset($aConnections[$oConnectionProvider->getName()]) ? $aConnections[$oConnectionProvider->getName()] : false;
	}
	
	public function getConnectionFacebook() {
		$oConnection = UserManager::getProviderFacebook();
		return $oConnection ? $this->getConnection($oConnection) : false;
	}
	
	public function getConnectionTwitter() {
		$oConnection = UserManager::getProviderTwitter();
		return $oConnection ? $this->getConnection($oConnection) : false;
	}
	
	public function removeConnection(UserConnection $oConnection) {
		if(isset($this->m_aConnections[$oConnection->getProvider()])) {
			$oConnection->delete();
			unset($this->m_aConnections[$oConnection->getProvider()]);
		}
	}
	
	/**
	 * Get a list with all currently associated email adress for this user.
	 * 
	 * @return array<UserEmail>
	 */
	public function getEmails() {
		if($this->m_aEmails === false) {
			$this->m_aEmails = array();
			$oStatement = UserManager::getDatabaseConnection()->select('user_email', $this->getId(), 'userId');
			$aData = UserEmail::loadObjectList(UserManager::getTableUserEmail(), $oStatement);
			foreach($aData as $oEmail) {
				$this->m_aEmails[Encoding::toLower($oEmail->getEmail())] = $oEmail;
			}
		}
		return $this->m_aEmails;
	}
	
	/**
	 * Create a new UserEmail object for this user.
	 * 
	 * @see UserEmail::create()
	 * @param string $sEmail
	 * @param boolean $bVerified
	 * @return UserEmail|NULL
	 */
	public function createEmail($sEmail, $bVerified = false) {
		return UserEmail::create($this, $sEmail, $bVerified);
	}
	
	/**
	 * Add an UserEmail to this user.
	 * If the userId of the UserEmail does nat match the id of this user,
	 * this function will return false.
	 * 
	 * @param UserEmail $oEmail
	 * @return boolean
	 */
	public function addEmail(UserEmail $oEmail) {
		// Return false if userId does not match
		if($oEmail->getUserId() != $this->getId())
			return false;

		// Only add mail when not yet in list
		$this->getEmails();
		$sKey = Encoding::toLower($oEmail->getEmail());
		if(!isset($this->m_aEmails[$sKey]))
			$this->m_aEmails[$sKey] = $oEmail;
		
		return true;
	}
	
	/**
	 * Get an UuserEmail based on a given email-adress if any, or the first email adress for this user.
	 * 
	 * @param string $sEmail
	 * @return UserEmail|null
	 */
	public function getEmail($sEmail = null) {
		$aEmails = $this->getEmails();
		if($sEmail)
			return isset($aEmails[Encoding::toLower($sEmail)]) ? $aEmails[Encoding::toLower($sEmail)] : null;
		else 
			return count($aEmails) > 0 ? array_first($aEmails) : null;
	}
	
	/**
	 * Remove the given UserEmail for this user.
	 * If the userId of the UserEmail does nat match the id of this user,
	 * this function will return false.
	 * 
	 * @see UserEmail::delete()
	 * @param UserEmail $oEmail
	 * @return boolean
	 */
	public function removeEmail(UserEmail $oEmail) {
		// Return if userId does not match
		if($oEmail->getUserId() != $this->getId())
			return false;
		
		// Delete instance
		$oEmail->delete();
		
		// Remove from list
		$sKey = Encoding::toLower($oEmail->getEmail());
		if(isset($this->m_aEmails[$sKey]))
			unset($this->m_aEmails[$sKey]);
		
		return true;
	}

	/**
	 * Get a list with all UserSession-objects associated for the user.
	 * 
	 * @return array<UserSession>
	 */
	public function getSessions() {
		if($this->m_aSessions === false) {
			$this->m_aSessions = array();
			$oStatement = UserManager::getDatabaseConnection()->select('user_session', $this->getId(), 'userId');
			$aData = UserEmail::loadObjectList(UserManager::getTableUserSession(), $oStatement);
			foreach($aData as $oSession) {
				$this->m_aSessions[$oSession->getToken()] = $oSession;
			}
		}
		return $this->m_aSessions;
	}
	
	/**
	 * Create a new UserSession object for the user.
	 * 
	 * @param string $sIp
	 * @param string $sUserAgent
	 * @return UserSession|null
	 */
	public function createSession($sIp, $sUserAgent) {
		return UserSession::create($this, $sIp, $sUserAgent);
	}
	
	/**
	 * Add the UserSession to the user.
	 * If the userId of the UserSession does nat match the id of this user,
	 * this function will return false.
	 * 
	 * @param UserSession $oSession
	 * @return boolean
	 */
	public function addSession(UserSession $oSession) {
		if($oSession->getUserId() != $this->getId())
			return false;
		
		$this->getSessions();
		if(!isset($this->m_aSessions[$oSession->getToken()]))
			$this->m_aSessions[$oSession->getToken()] = $oSession;
		
		return true;
	}
	
	/**
	 * Get a UserSession by it's token.
	 * If no tolen is given, return the first element of te list.
	 * 
	 * @param string $sToken
	 * @return UserSession|null
	 */
	public function getSession($sToken = null) {
		$aSession = $this->getSessions();
		if($sToken)
			return isset($aSessions[$sToken]) ? $aSessions[$sToken] : null;
		else 
			return count($aSession) > 0 ? array_first($aSession) : null;
	}
	
	/**
	 * Remove a given UserSession for the user.
	 * If the userId of the UserSession does nat match the id of this user,
	 * this function will return false.
	 * 
	 * @param UserSession $oSession
	 * @return boolean
	 */
	public function removeSession(UserSession $oSession) {
		if($oSession->getUserId() != $this->getId())
			return false;
		
		// Delete instance
		$oSession->delete();
		
		// Remove from list
		if(isset($this->m_aSessions[$oSession->getToken()]))
			unset($this->m_aSessions[$oSession->getToken()]);
		
		return true;
	}
	
	public function clearSessions() {
		$this->getSessions();
		foreach($this->m_aSessions as $oSession) {
			$oSession->delete();
		}
		$this->m_aSessions = array();
	}
	
	/**
	 * Encode the given password with the user's hash and an ID based randomizer.
	 * 
	 * @param string $mValue
	 * @return string (length: 32)
	 */
	public function encodePassword($mValue) {
		$sHash = Encoding::substring($this->getHash(), ($this->getId() / 3) % 16, ($this->getId() / 2) % 16);
		$sData = sprintf('%s.%s.%s', $this->getId(), $mValue, $sHash);
		return md5($sData);
	}
	
	/**
	 * Try to load an existing user by it's ID.
	 * 
	 * @param int $nId
	 * @return User|null
	 */
	public static function load($nId) {
		return self::loadObject(UserManager::getTableUser(), $nId);
	}
	
	/**
	 * Try to create a new user.
	 * 
	 * @see UserManager::isValidName()
	 * @param string $sName The (user-)name should be unique and match UserManager::isValidName().
	 * @param boolean $bVerified Is this user cerified upon creation (default: false).
	 * @return User|null
	 */
	public static function create($sName, $bVerified = false) {
		if(!UserManager::isValidName($sName))
			return null;
		
		return self::createObject(UserManager::getTableUser(), array(
			'name' => $sName,
			'hash' => md5(mt_rand() . $sName . microtime()),
			'verified' => $bVerified ? 1 : 0
		));	
	}
}
