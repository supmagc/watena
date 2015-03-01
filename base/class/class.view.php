<?php

/**
 * Default parent view class.
 * Configuration:
 * - charset: defaults to Encoding::charset()
 * - content-type: defaults to text/plain
 * - gzip: defaults to true
 * 
 * @author Jelle Voet
 * @version 0.1.0
 */
abstract class View extends CacheableData {
	
	abstract public function headers(Model $oModel = null);
	abstract public function render(Model $oModel = null);
	public function requiredModelType() {return null;}
	
	private $m_sCharset = null;
	private $m_sContentType = null;
	
	public final function getCharset() {
		return $this->m_sCharset ?: $this->getConfig('charset', Encoding::charset());
	}
	
	public final function getContentType() {
		return $this->m_sContentType ?: $this->getConfig('content-type', 'text/plain');
	}
	
	protected final function setContentType($sContentType = null, $sCharset = null) {
		if($sCharset) $this->m_sCharset = $sCharset;
		if($sContentType) $this->m_sContentType = $sContentType;
		return Output::header(sprintf('Content-Type: %s;charset=%s', $this->getContentType(), $this->getCharset()));
	}
}
