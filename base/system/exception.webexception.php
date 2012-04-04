<?php

class WebException extends Exception {

	private $m_oRequest;

	public function __construct(WebRequest $oRequest) {
		parent::__construct(curl_error($oRequest->getCurl()), curl_errno($oRequest->getCurl()));
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