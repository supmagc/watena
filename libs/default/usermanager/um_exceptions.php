<?php

class UserInvalidIdException extends Exception {

	public function __construct() {
		parent::__construct('No record could be retrieved for the given id.');
	}
}

class UserDuplicateEmailException extends Exception {
	
	public function __construct() {
		parent::__construct('A user_email-record with the same email allready exists.');
	}
}

class UserDuplicateNameException extends Exception {
	
	public function __construct() {
		parent::__construct('A user-record with the same name allready exists.');
	}
}

class UserDuplicateLoginException extends Exception {
	
	public function __construct() {
		parent::__construct('A user with a different login is allready logged in.');
	}
}

class UserConnectionProviderFailed extends Exception {
	
	public function __construct() {
		parent::__construct('Unable to initialize the ConnectionProvider-object.');
	}
}

class UserInvalidNameException extends Exception {
	
}

class UserInvalidEmailException extends Exception {

}

class UserUnverifiedEmailException extends Exception {
	
}
?>