<?php

class WebException extends Exception {

	private $m_oRequest;

	public function __construct(WebRequest $oRequest) {
		parent::__construct($oRequest ? curl_error($oRequest->getCurl()) : 'An unknown CURL-error happened!', $oRequest ? curl_errno($oRequest->getCurl()) : 0);
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