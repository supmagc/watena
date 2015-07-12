<?php namespace Watena\Libs\Base\Ipco;

class ContentParserPart {
	
	private $m_nStart;
	private $m_nLength;
	private $m_sMethod;
	private $m_aParams; 
	
	public function __construct($nStart, $nLength, $sMethod, array $aParams = array()) {
		$this->m_nStart = $nStart;
		$this->m_nLength = $nLength;
		$this->m_sMethod = $sMethod;
		$this->m_aParams = $aParams;
	}
	
	public function getStart() {
		return $this->m_nStart;
	}
	
	public function getLength() {
		return $this->m_nLength;
	}
	
	public function getMethod() {
		return $this->m_sMethod;
	}
	
	public function getParams() {
		return $this->m_aParams;
	}
}
