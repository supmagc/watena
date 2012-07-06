<?php

class UserException extends WatCeption {
	
}

class UserInvalidIdException extends UserException {

	public function __construct() {
		parent::__construct('No record could be retrieved for the given id.');
	}
}

class UserInvalidNameException extends UserException {

	private $m_sName;

	public function __construct($sName) {
		parent::__construct('Invalid name: {name}', array('name' => $sName));
		$this->m_sName = $sName;
	}

	public function getName() {
		return $this->m_sName;
	}
}

class UserInvalidEmailException extends UserException {

	private $m_sEmail;

	public function __construct($sEmail) {
		parent::__construct('Invalid email: {email}', array('email' => $sEmail));
		$this->m_sEmail = $sEmail;
	}

	public function getEmail() {
		return $this->m_sEmail;
	}
}

class UserInvalidPasswordException extends UserException {

	private $m_sPassword;

	public function __construct($sPassword) {
		parent::__construct('Invalid password: {password}', array('password' => $sPassword));
		$this->m_sPassword = $sPassword;
	}

	public function getPassword() {
		return $this->m_sPassword;
	}
}

class UserDuplicateEmailException extends UserException {
	
	private $m_sEmail;
	
	public function __construct($sEmail) {
		parent::__construct('A user_email-record with the same email allready exists for another user: {email}', array('email' => $sEmail));
		$this->m_sEmail = $sEmail;
	}
	
	public function getEmail() {
		return $this->m_sEmail;
	}
}

class UserDuplicateNameException extends UserException {
	
	private $m_sName;
	
	public function __construct($sName) {
		parent::__construct('A user-record with the same name allready exists for another user: {name}', array('name' => $sName));
		$this->m_sName = $sName;
	}
	
	public function getName() {
		return $this->m_sName;
	}
}

class UserDuplicateUserException extends UserException {
	
	private $m_oUserA;
	private $m_oUserB;
	
	public function __construct(User $oUserA, User $oUserB) {
		parent::__construct('Another user is currently logged in and can\'t be matched.');
		$this->m_oUserA = $oUserA;
		$this->m_oUserB = $oUserB;
	}
	
	public function getUserA() {
		return $this->m_oUserA;
	}
	
	public function getUserB() {
		return $this->m_oUserB;
	}
}

class UserConnectionProviderInitializeFailed extends UserException {
	
	public function __construct() {
		parent::__construct('Unable to initialize the ConnectionProvider-object.');
	}
}

class UserUnknownNameException extends UserException {

	private $m_sName;

	public function __construct($sName) {
		parent::__construct('Unknown name: {name}', array('name' => $sName));
		$this->m_sName = $sName;
	}

	public function getName() {
		return $this->m_sName;
	}
}

class UserUnknownEmailException extends UserException {

	private $m_sEmail;

	public function __construct($sEmail) {
		parent::__construct('Unknown email: {email}', array('email' => $sEmail));
		$this->m_sEmail = $sEmail;
	}

	public function getEmail() {
		return $this->m_sEmail;
	}
}

class UserNoPasswordException extends UserException {

	private $m_oUser;

	public function __construct(User $oUser) {
		parent::__construct('User has no password password: {name}', array('name' => $oUser->getName()));
		$this->m_oUser = $oUser;
	}

	public function getUser() {
		return $this->m_oUser;
	}
}

class UserUnverifiedEmailException extends UserException {
	
	private $m_oEmail;
	
	public function __construct(UserEmail $oEmail) {
		parent::__construct('A user with this email-adress exists, but is unverified: {email}', array('email' => $oEmail->getEmail()));
		$this->m_oEmail = $oEmail;
	}
	
	public function getEmail() {
		return $this->m_oEmail;
	}
}

class UserUnverifiedUserException extends UserException {
	
	private $m_oUser;
	
	public function __construct(User $oUser) {
		parent::__construct('A user exists, but is unverified: {name}', array('name' => $oUser->getName()));
		$this->m_oUser = $oUser;
	}
	
	public function getUser() {
		return $this->m_oUser;
	}
}
?>