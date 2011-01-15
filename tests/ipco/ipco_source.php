<?php

class IPCO_Source extends IPCO_Base {

	private $m_sContent;
	private $m_nIndex;
	private $m_nLength;
	
	public function __construct($sFileName, IPCO $ipco) {
		parent::__construct($ipco);
		$this->m_sContent = file_get_contents(parent::getIpco()->getSourcePath($sFileName));
		$this->m_nIndex = -1;
		$this->m_nLength = strlen($this->m_sContent);
	}
	
	public function read() {
		return ++$this->m_nIndex < $this->m_nLength;
	}
	
	public function get() {
		return substr($this->m_sContent, $this->m_nIndex, 1);
	}
}

?>