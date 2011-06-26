<?php

class IPCO_Base {
	
	private $m_ipco;
	
	public function __construct(IPCO $ipco) {
		$this->m_ipco = $ipco;
	}
	
	public function getIpco() {
		return $this->m_ipco;
	}
}

?>