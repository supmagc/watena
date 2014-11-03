<?php 

class EventCallbackException extends WatCeption {
	
	public function __construct($sEvent, $cbEvent, Exception $oInnerException) {
		parent::__construct('Callback {callback} for event {event} triggered an exception.', array('event' => $sEvent, 'callback' => $cbEvent), null, $oInnerException);
	}
}
?>