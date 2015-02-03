<?php

class User extends UserManagerVerifiable {
	
	private $m_aConnections = null;
	private $m_aSessions = null;
	private $m_aEmails = null;
	
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
	 * The value should be a valid php timezone, of which you can create a Time-object.
	 * 
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
	 * 
	 * @param string $mValue
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
	 * 
	 * @param string $mValue
	 * @return boolean
	 */
	public function setLastname($mValue) {
		$mValue = Encoding::trim($mValue);
		$mValue = Encoding::substring($mValue, 0, 64);
		$mValue = Encoding::trim($mValue);
		if(empty($mValue)) $mValue = null;
		return $this->setDataValue('lastname', $mValue);
	}
		
	public function setTimezone($mValue) {
		$this->setDataValue('timezone', Time::formatTimezone($mValue));
		return true;
	}
	
	public function setLocale($mValue) {
		if(Encoding::regMatch('[a-z]{2}(_[a-z]{2})?', '' . $mValue)) {
			$this->setDataValue('locale', $mValue);
			return true;
		}
		return false;
	}

	public function setPassword($mValue) {
		if(UserManager::isValidPassword($mValue)) {
			$this->setDataValue('password', $this->encodePassword($mValue));
			return true;
		}
		return false;
	}
	
	public function verifyPassword($mValue) {
		return $this->getDataValue('password') === $this->encodePassword($mValue);
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
	
	public function getEmails() {
		if($this->m_aEmails === null) {
			$this->m_aEmails = array();
			$oStatement = UserManager::getDatabaseConnection()->select('user_email', $this->getId(), 'userId');
			foreach($oStatement as $aData) {
				$oEmail = UserEmail::load($aData);
				$this->m_aEmails[$oEmail->getEmail()] = $oEmail;
			}
		}
		return $this->m_aEmails;
	}
	
	public function addEmail($sEmail, $bVerified = false) {
		if($oEmail = UserEmail::create($this, $sEmail, $bVerified)) {
			$this->m_aEmails[Encoding::toLower($sEmail)] = $oEmail;
		}
		return $this->getEmail($sEmail);
	}
	
	public function getEmail($sEmail = null) {
		$aEmails = $this->getEmails();
		if($sEmail)
			return isset($aEmails[Encoding::toLower($sEmail)]) ? $aEmails[Encoding::toLower($sEmail)] : false;
		else 
			return count($aEmails) > 0 ? array_first($aEmails) : false;
	}
	
	public function removeEmail(UserEmail $oEmail) {
		if(isset($this->m_aEmails[$oEmail->getEmail()])) {
			$oEmail->delete();
			unset($this->m_aEmails[$oEmail->getEmail()]);
		}
	}
	
	public function getSessions() {
		if($this->m_aSessions === null) {
			$this->m_aSessions = array();
			$oStatement = UserManager::getDatabaseConnection()->select('user_session', $this->getId(), 'userId');
			foreach($oStatement as $aData) {
				$oSession = UserSession::load($this, $aData);
				$this->m_aSessions[$oSession->getToken()] = $oSession;
			}
		}
		return $this->m_aSessions;
	}
	
	public function getSession($sToken) {
		$this->getSessions();
		$sToken = Encoding::toLower($sToken);
		return isset($this->m_aSessions[$sToken]) ? $this->m_aSessions[$sToken] : false;
	}
	
	public function createSession($sIp, $sUserAgent) {
		$oSession = UserSession::create($this, $sIp, $sUserAgent);
		$this->getSessions();
		$this->m_aSessions[$oSession->getToken()] = $oSession;
	}
	
	public function removeSession(UserSession $oSession) {
		// TODO
	}
	
	public function removeSessions() {
		// TODO
	}
	
	public function encodePassword($mValue) {
		$sHash = Encoding::substring($this->getHash(), ($this->getId() / 3) % 16, ($this->getId() / 2) % 16);
		$sData = sprintf('%s.%s.%s', $this->getId(), $mValue, $sHash);
		return md5($sData);
	}
	
	/**
	 * Try to load an existing user by it's ID.
	 * 
	 * @param int $nId
	 * @return null|User
	 */
	public static function load($nId) {
		return self::loadObject(UserManager::getTableUser(), $nId);
	}
	
	/**
	 * Create a new user.
	 * 
	 * @see UserManager::isValidName()
	 * @param string $sName The (user-)name should be unique and match UserManager::isValidName().
	 * @param boolean $bVerified Is this user cerified upon creation (default: false).
	 * @return null|User
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
