<?php

class WatCeption extends Exception {
	
	public function __construct($sMessage, array $aData = array(), Object $oContext = null, Exception $oInnerException) {
		parent::__construct('A \'Watena\'-error occured. Contact an administrator if you are unable to continue.', null);
	}
}

?>