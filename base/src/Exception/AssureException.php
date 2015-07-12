<?php namespace Watena\Core;

class AssureException extends WatCeption {
	
	public function __construct($sMessage, Object $oContext = null, Exception $oInnerException = null) {
		parent::__construct($sMessage, array(), $oContext, $oInnerException);
	}
}
