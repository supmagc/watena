<?php
/**
 * Class representing a database user's email.
 * 
 * @author Jelle Voet
 * @version 0.2.0
 */
class UserEmail extends UserManagerVerifiable {
	
	private $m_oUser = false;
	private $m_oTime = false;

	public function getKeyForContainer(Container $oContainer) {
		return Encoding::toLower($this->getDataValue('email'));
	}
		
	/**
	 * Get the userId for this email.
	 * 
	 * @return int
	 */
	public function getUserId() {
		return $this->getDataValue('userId');
	}
	
	/**
	 * Get the user instance or null if Id is invalid.
	 * 
	 * @return User|null
	 */
	public function getUser() {
		if(false === $this->m_oUser) {
			$this->m_oUser = User::load($this->getDataValue('userId'));
		}
		return $this->m_oUser;
	}
	
	/**
	 * Get the registered email-adress.
	 * 
	 * @return string
	 */
	public function getEmail() {
		return $this->getDataValue('email');
	}

	/**
	 * Get the time this emailadress was created.
	 * 
	 * @return Time
	 */
	public function getTime() {
		if(false === $this->m_oTime) {
			$this->m_oTime = new Time($this->getDataValue('timestamp'));
		}
		return $this->m_oTime;
	}
	
	/**
	 * Try to load an existing UserEmail by it's ID.
	 * 
	 * @param int $mId
	 * @return UserEmail|null
	 */
	public static function load($mId) {
		$oInstance = self::loadObject(UserManager::getTableUserEmail(), $mId);
		$oInstance->getUser()->addEmail($oInstance);
		return $oInstance;
	}
	
	/**
	 * Try to create a new UserEmail.
	 * 
	 * @see UserManager::isValidEmail()
	 * @param User $oUser
	 * @param string $sEmail
	 * @param boolean $bVerified
	 * @return UserEmail|null
	 */
	public static function create(User $oUser, $sEmail, $bVerified = false) {
		if(UserManager::getUserIdByEmail($sEmail) || !UserManager::isValidEmail($sEmail))
			return null;

		$oInstance = self::createObject(UserManager::getTableUserEmail(), array(
			'userId' => $oUser->getId(),
			'email' => $sEmail,
			'verified' => $bVerified ? 1 : 0
		));
		
		$oUser->getContainerMails()->addItem($oInstance);
// 		$oUser->addEmail($oInstance);
		return $oInstance;
	}
}
