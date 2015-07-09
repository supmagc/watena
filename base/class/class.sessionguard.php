<?php namespace Watena\Core;

/**
 * This class protects our session
 * 
 * @author Jelle Voet
 * @version 1.0.0 RC1
 *
 */
class SessionGuard {
	
	const VALID_OK = 0;
	const VALID_NOBROWSERMATCH = 1;
	const VALID_NOTEMPORARY = 2;
	const VALID_NOTEMPORARYMATCH = 3;
			
	private $m_nSuspicion = 0;
	private $m_nPageCount = 0;
	private $m_nLastRequest = 0;
	private $m_sLastRequest = null;
	private $m_sPrivateKey = null;
	private $m_sBrowserKey = null;
	private $m_sTemporary = 'unknown';
	
	private function __construct() {
		$this->m_nPageCount = 0;
		$this->m_nSuspicion = 0;
		$this->m_nLastRequest = time();
		$this->m_sLastRequest = URI::Request();
		$this->m_sPrivateKey = self::GenerateKey();
		$this->m_sBrowserKey = self::GenerateBrowserKey($this->m_sPrivateKey);
		$this->ComputeAndSetTemporaryKey();
		$_SESSION['GUARD'] = $this;
	}
	
	public function __destruct() {
		$_SESSION['GUARD'] = $this;
	}
	
	private function ComputeAndSetTemporaryKey() {
		$this->m_sTemporary = substr(md5($this->m_sPrivateKey . $this->m_nPageCount . $this->m_sLastRequest), 1, 26);
		setcookie('SG_TEMPORARY', $this->m_sTemporary, 0, URI::HostOffset(true), URI::Domain(), false, true);		
	}
	
	private function Validate() {
		$nCode = self::VALID_OK;
		if($this->m_sBrowserKey !== self::GenerateBrowserKey($this->m_sPrivateKey)) {
			$nCode = self::VALID_NOBROWSERMATCH;
		}
		elseif(!isset($_COOKIE['SG_TEMPORARY'])) {
			$nCode = self::VALID_NOTEMPORARY;
		}
		elseif($_COOKIE['SG_TEMPORARY'] !== $this->m_sTemporary) {
			$nCode = self::VALID_NOTEMPORARYMATCH;
		}
		if(time() - $this->m_nLastRequest > 15 || URI::Request() != $this->m_sLastRequest) {
			$this->m_nPageCount += 1;
			$this->m_sLastRequest = URI::Request();
			$this->ComputeAndSetTemporaryKey();			
			if(rand(0, 6) === 5) {
				session_regenerate_id(true);
			}
		}
		$this->m_nLastRequest = time();
		return $nCode;
	}
	
	public static function Load($sHandler, $sSavePath) {
		ini_set('session.save_handler', $sHandler);
		ini_set('session.save_path', $sSavePath);
		ini_set('session.use_only_cookies', true);
		session_name('SG_PERMANENT');
		session_set_cookie_params(0, URI::HostOffset(true), URI::Domain(), false, true);
		session_start();
		$oGuard = null;
		if(isset($_SESSION['GUARD'])) {
			$oGuard = $_SESSION['GUARD'];
			if(($nValid = $oGuard->Validate()) !== self::VALID_OK) {
				$_SESSION = array();
				$oGuard = null;
			}
		}
		if($oGuard === null) $oGuard = new SessionGuard();
		return $oGuard;
	}
	
	private static function GenerateBrowserKey($sPrivateKey) {
		$sAccept = isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : 'Accept';
		$sUserAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'UserAgent';
		$sAcceptLanguage = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : 'EN';
		$sAcceptEncoding = isset($_SERVER['HTTP_ACCEPT_ENCODING']) ? $_SERVER['HTTP_ACCEPT_ENCODING'] : 'Encoding';
		return md5($sPrivateKey . $sAccept . $sUserAgent . $sAcceptLanguage . $sAcceptEncoding);
	}
	
	private static function GenerateKey() {
		$sData = microtime() . (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
		for($i=0 ; $i<32 ; ++$i) $sData .= chr(rand(0, 256));
		return md5($sData);
	}
}
