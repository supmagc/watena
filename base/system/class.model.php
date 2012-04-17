<?php

abstract class Model extends CacheableData {

	private $m_sCharset = null;
	private $m_sContentType = 'text/plain';
	
	public final function setCharset($sCharset) {
		$this->m_sCharset = $sCharset;
	}
	
	public final function setContentType($sContentType) {
		$this->m_sContentType = $sContentType;
	}
	
	public final function getCharset() {
		return $this->m_sCharset ? $this->m_sCharset : Encoding::charset();
	}
	
	public final function getContentType() {
		return $this->m_sContentType;
	}
}

?>