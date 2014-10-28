<?php

class OutputControl extends Object {
	
	public final function validateCache(Model $oModel) {
		$bReturn = false;
		
		// Check if page should be served from cache
		if(!watena()->isDebug()) {
			$oModified = new Time(Request::lastModified());
			if(Request::lastModified() && $oModified->getTimestamp() >= $oModel->getLastModified()) {
				header('HTTP/1.0 304 Not Modified');
				$bReturn = true;
			}
			
			if(Request::eTag() && Request::eTag() == $oModel->getCacheTag()) {
				header('HTTP/1.0 304 Not Modified');
				$bReturn = true;
			}
		}

		// If no early return, try to set the caching headers
		$nDuration = $oModel->getCacheDuration();
		if(!watena()->isDebug() && $nDuration > 0) {
			// Expires if the more simple (and older) solution for cache control.
			// This header comes form the original HTTP specifications.
			$oExpires = Time::getUtcTime()->add(new Interval(0, 0, 0, 0, 0, $nDuration));
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
			$this->header('Cache-Control: max-age=0, must-revalidate, private, no-store, no-cache');
		}
		
		return $bReturn;
	}
	
	public final function validateCompression(Model $oModel) {
		if(watena()->getConfig()->compression() && $oModel->hasCompressionSupport()) {
			if(Request::compressionSupport('gzip')) {
				ob_start(array($this, '_compressGzip'));
			}
			else if(Request::compressionSupport('deflate')) {
				ob_start(array($this, '_compressDeflate'));
			}
		}
	}
	
	public final function _compressGzip($sContent) {
		$sContent = gzencode($sContent, 5);
		$this->header('Content-Length: ' . Encoding::length($sContent));
		$this->header('Content-Encoding: gzip');
		return $sContent;
	}
	
	public final function _compressDeflate($sContent) {
		$sContent = gzdeflate($sContent, 5);
		$this->header('Content-Length: ' . Encoding::length($sContent));
		$this->header('Content-Encoding: deflate');
		return $sContent;
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