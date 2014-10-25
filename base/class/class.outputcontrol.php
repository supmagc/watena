<?php

class OutputControl extends Object {
	
	public final function validateCache(Model $oModel) {
		// Check if page should be served from cache
		if(Request::lastModified() && Request::lastModified() >= $oModel->getLastModified()) {
			header('HTTP/1.0 304 Not Modified');
			return true;
		}
		
		if(Request::eTag() && Request::eTag() == $oModel->getCacheTag()) {
			header('HTTP/1.0 304 Not Modified');
			return true;
		}

		// If no early return, try to set the caching headers
		$nDuration = $oModel->getCacheDuration();
		if($nDuration > 0) {
			// Expires if the more simple (and older) solution for cache control.
			// This header comes form the original HTTP specifications.
			$oExpires = Time::getUtcTime();
			$oExpires->add(new Interval(0, 0, 0, 0, 0, $nDuration));
			$this->header('Expires: '.$oExpires->formatRfc1123());
		
			// Cache-Control enables a more fine-grained level of control
			// This header is a later html specification: HTTP 1.1
			$this->header('Cache-Control: max-age='.$nDuration.', must-revalidate, public');
			
			// Set last-modified validator
			if($oModel->getLastModified()) {
				$oModified = Time::createUtcTime($oModel->getLastModified());
				$this->header('Last-Modified: ' . $oModified->formatRfc1123());
			}
						
			// Set etag validator
			if($oModel->getCacheTag()) {
				$this->header('ETag: ' . $oModel->getCacheTag());
			}
		}
		// No caching required
		else {
			$this->header('Expires: 0');
			$this->header('Cache-Control: no-store, no-cache, private');
		}
		
		return false;
	}
	
	public final function validateCompression(Model $oModel) {
		
	}
	
	protected final function setContentType($sContentType = null, $sCharset = null) {
		if($sCharset) $this->m_sCharset = $sCharset;
		if($sContentType) $this->m_sContentType = $sContentType;
		return $this->header(sprintf('Content-Type: %s;charset=%s', $this->getContentType(), $this->getCharset()), true);
	}
	
	public final function header($sLine, $bOverwrite = true) {
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