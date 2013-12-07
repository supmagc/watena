<?php

class WebException extends WatCeption {

	private $m_oRequest;

	public function __construct(WebRequest $oRequest = null) {
		if($oRequest)
			parent::__construct('An error occured while processing a WebRequest/-Response {code}: {message}', array('message' => curl_error($oRequest->getCurl()), 'code' => curl_errno($oRequest->getCurl())));
		else
			parent::__construct('An unknown WebRequest/-Response error occured!');
		$this->m_oRequest = $oRequest;
	}

	public function __destruct() {
		$this->m_oRequest = null;
	}

	public function getRequest() {
		return $this->m_oRequest;
	}
}

?>