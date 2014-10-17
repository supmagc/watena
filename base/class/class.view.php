<?php

/**
 * Default parent view class.
 * Configuration:
 * - charset: defaults to Encoding::charset()
 * - content-type: defaults to text/plain
 * - gzip: defaults to true
 * 
 * @author Jelle
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
	
	public final function validateCache(Model $oModel) {
		if(Request::lastModified() && Request::lastModified() >= $oModel->getLastModified()) {
			header('HTTP/1.0 304 Not Modified');
			return true;
		}
		
		if(Request::eTag() && Request::eTag() == $oModel->getETag()) {
			header('HTTP/1.0 304 Not Modified');
			return true;
		}
		
		return false;
	}
	
	public final function setCaching($nDuration = 0, $mLastModified = null, $sETag = null) {
		if($nDuration > 0) {
			// Expires if the more simple (and older) solution for cache control.
			// This header comes form the original HTTP specifications.
			$oExpires = Time::getUtcTime();
			$oExpires->add(new Interval(0, 0, 0, 0, 0, $nDuration));
			$this->header('Expires: '.$oExpires->formatRfc1123());
		
			// Cache-Control enables a more fine-grained level of control
			// This header is a later html specification: HTTP 1.1
			$this->header('Cache-Control: max-age='.$nDuration.', must-revalidate, public');
			
			if(null !== $mLastModified) {
				$oModified = Time::createUtcTime($mLastModified);
				$this->header('Last-Modified: ' . $oModified->formatRfc1123());
			}
		}
		else {
			$this->header('Expires: 0');
			$this->header('Cache-Control: no-store, no-cache, private');
		}
				
		// These are the validators that will control the validity of the cache
		$this->header('Last-Modified: max-age='.$nDuration.', must-revalidate');
		
		header('Cache-Control: max-age='.(3600 * 24).', must-revalidate, public');
		header('Content-type: text/css');
		header('Expires: ' . gmdate('D, d M Y H:i:s', time() + (3600 * 168)) . ' GMT');
		header('Last modified: ' . (file_exists($file) ? filemtime($file) : gmdate('D, d M Y H:i:s', time())) . ' GMT');
	} 
	
	protected final function setContentType($sContentType = null, $sCharset = null) {
		if($sCharset) $this->m_sCharset = $sCharset;
		if($sContentType) $this->m_sContentType = $sContentType;
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
			$this->getLogger()->warning('Headers are allready sent at {file} (line: {line})', array('file' => $sFile, 'line' => $nLine));
		}
		return false;
	}
}

?>