<?php

abstract class View extends CacheableData {
	
	abstract public function headers(Model $oModel = null);
	abstract public function render(Model $oModel = null);
	
	private $m_sCharset = null;
	private $m_sContentType = null;
	
	public final function getCharset() {
		return $this->m_sCharset ? $this->m_sCharset : $this->getConfig('charset', Encoding::charset());
	}
	
	public final function getContentType() {
		return $this->m_sContentType ? $this->m_sContentType : $this->getConfig('content-type', 'text/plain');
	}
	
	protected final function headerContentType($sContentType = null, $sCharset = null) {
		$this->m_sCharset = $sCharset;
		$this->m_sContentType = $sContentType;
		return $this->header(sprintf('Content-Type: %s;charset=%s', $this->getContentType(), $this->getCharset()), true);
	}
	
	protected final function header($sLine, $bOverwrite = false) {
		$sFile = '';
		$nLine = 0;
		if(!headers_sent($sFile, $nLine)) {
			header($sLine, $bOverwrite);
			return true;
		}
		else {
			$this->getLogger()->warning('Headers are allready sent at {file} (line: {line}.', array('file' => $sFile, 'line' => $nLine));
		}
		return false;
	}
}

?>