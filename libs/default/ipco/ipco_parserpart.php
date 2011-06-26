<?php

class IPCO_ParserPart {
	
	private $m_nStart;
	private $m_nLength;
	private $m_sCallbackIdentifier;
	
	public function __construct($nStart, $nLength, $sCallbackIdentifier) {
		$this->m_nStart = $nStart;
		$this->m_nLength = $nLength;
		$this->m_sCallbackIdentifier = $sCallbackIdentifier;
	}
}

?>