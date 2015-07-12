<?php namespace Watena\Libs\Base\Ipco;

class Base {
	
	private $m_oIpco;
	
	public function __construct(IPCO $oIpco) {
		$this->m_oIpco = $oIpco;
	}
	
	public function getIpco() {
		return $this->m_oIpco;
	}
}
