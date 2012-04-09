<?php

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

class UserDuplicateConnectionException extends Exception {
	
	public function __construct() {
		parent::__construct('A user-connection-record for the same connection allready exists.');
	}
}

class UserDuplicateLoginException extends Exception {
	
	public function __construct() {
		parent::__construct('A user with a different login is allready logged in.');
	}
}

class UserInvalidIdException extends Exception {
	
	public function __construct() {
		parent::__construct('No record could be retrieved for the given id.');
	}
}

?>