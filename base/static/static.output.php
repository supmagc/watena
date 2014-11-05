<?php

class Output {
	
	public final static function validateCache(Model $oModel) {
		$bReturn = false;
		
		// Check if page should be served from cache
		if(!isDebug()) {
			$oModified = new Time(Request::lastModified());
			if(Request::lastModified() && $oModified->getTimestamp() >= $oModel->getLastModified()) {
				self::header('HTTP/1.0 304 Not Modified');
				$bReturn = true;
			}
			
			if(Request::eTag() && Request::eTag() == $oModel->getCacheTag()) {
				self::header('HTTP/1.0 304 Not Modified');
				$bReturn = true;
			}
		}

		// If no early return, try to set the caching headers
		$nDuration = $oModel->getCacheDuration();
		if(!isDebug() && $nDuration > 0) {
			// Expires if the more simple (and older) solution for cache control.
			// This header comes form the original HTTP specifications.
			$oExpires = Time::getUtcTime()->add(new Interval(0, 0, 0, 0, 0, $nDuration));
			self::header('Expires: '.$oExpires->formatRfc1123());
		
			// Cache-Control enables a more fine-grained level of control
			// This header is a later html specification: HTTP 1.1
			self::header('Cache-Control: max-age='.$nDuration.', must-revalidate, public');
			
			// Set last-modified validator
			if($oModel->getLastModified()) {
				$oModified = Time::createUtcTime($oModel->getLastModified());
				self::header('Last-Modified: ' . $oModified->formatRfc1123());
			}
						
			// Set etag validator
			if($oModel->getCacheTag()) {
				self::header('ETag: ' . $oModel->getCacheTag());
			}
		}
		// No caching required
		else {
			self::header('Expires: 0');
			self::header('Cache-Control: max-age=0, must-revalidate, private, no-store, no-cache');
		}
		
		return $bReturn;
	}
	
	public final static function validateCompression(Model $oModel) {
		if(watena()->getConfig()->compression() && $oModel->compressionSupport()) {
			if(Request::compressionSupport('gzip')) {
				ob_start(array('Output', '_compressGzip'));
			}
			else if(Request::compressionSupport('deflate')) {
				ob_start(array('Output', '_compressDeflate'));
			}
		}
	}
	
	public final static function _compressGzip($sContent) {
		$sContent = gzencode($sContent, 5);
		self::header('Content-Length: ' . strlen($sContent)); // Use strlen since data is binary encoded
		self::header('Content-Encoding: gzip');
		return $sContent;
	}
	
	public final static function _compressDeflate($sContent) {
		$sContent = gzdeflate($sContent, 5);
		self::header('Content-Length: ' . strlen($sContent)); // Use strlen since data is binary encoded
		self::header('Content-Encoding: deflate');
		return $sContent;
	}
	
	public final static function header($sLine, $bOverwrite = true) {
		$sFile = '';
		$nLine = 0;
		if(!headers_sent($sFile, $nLine)) {
			header($sLine, $bOverwrite);
			return true;
		}
		else {
			Logger::getInstance('Output')->warning('Headers are allready sent at {file} (line: {line})', array('file' => $sFile, 'line' => $nLine));
		}
		return false;
	}
}

?>