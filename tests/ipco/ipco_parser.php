<?php

class IPCO_Parser extends IPCO_Base {
	
	private $m_sContent;
	
	public function __construct($sFileName, IPCO $ipco) {
		parent::__construct($ipco);
		$this->m_sContent = file_get_contents(parent::getIpco()->getSourcePath($sFileName));
	}
	
	public function parse() {
		
	}
}

?>