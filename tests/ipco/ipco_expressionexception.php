<?php

class IPCO_ExpressionException extends IPCO_Exception {
	
	public function __construct($sMessage, $sExpression) {
		parent::__construct($sMessage . "at <<$sExpression>>", parent::INVALIDEXPRESSION);
	}
}

?>