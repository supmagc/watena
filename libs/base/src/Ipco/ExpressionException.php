<?php namespace Watena\Libs\Base\Ipco;

class ExpressionException extends Exception {
	
	public function __construct($sMessage, $sExpression) {
		parent::__construct($sMessage . "at <<$sExpression>>", parent::INVALID_EXPRESSION);
	}
}
