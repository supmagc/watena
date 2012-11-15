<?php

class Url extends Object {
	
	private $m_sScheme;
	private $m_sUser;
	private $m_sPass;
	private $m_sHost;
	private $m_nPort;
	private $m_sPath;
	private $m_aParams;
	private $m_sAnchor;
	
	public final function __construct($sUrl) {
		$aData = parse_url($sUrl);
		if(is_array($aData)) {
			$this->m_sScheme = !empty($aData[PHP_URL_SCHEME]) ? $aData[PHP_URL_SCHEME] : 'http';
			$this->m_sUser = !empty($aData[PHP_URL_USER]) ? $aData[PHP_URL_USER] : null;
			$this->m_sPass = !empty($aData[PHP_URL_PASS]) ? $aData[PHP_URL_PASS] : null;
			$this->m_sHost = !empty($aData[PHP_URL_HOST]) ? $aData[PHP_URL_HOST] : 'localhost';
			$this->m_nPort = !empty($aData[PHP_URL_PORT]) ? (int)$aData[PHP_URL_PORT] : 80;
			$this->m_sPath = !empty($aData[PHP_URL_PATH]) ? $aData[PHP_URL_PATH] : '/';
			$this->m_aParams = !empty($aData[PHP_URL_QUERY]) ? parse_str($aData[PHP_URL_SCHEME]) : null;
			$this->m_sAnchor = !empty($aData[PHP_URL_FRAGMENT]) ? $aData[PHP_URL_FRAGMENT] : null;
		}
	}
	
	public final function hasAuthentication() {
		return $this->m_sUser !== null || $this->m_sPass !== null;
	}
	
	public final function hasParameters() {
		return count($this->m_aParams) > 0;
	}
	
	public final function hasAnchor() {
		return $this->m_sAnchor !== null;
	}
	
	public final function getScheme() {
		return $this->m_sScheme;
	}
	
	public final function getUser() {
		return $this->m_sUser;
	}
	
	public final function getPassword() {
		return $this->m_sPass;
	}
	
	public final function getHost() {
		return $this->m_sHost;
	}
	
	public final function getPort() {
		return $this->m_nPort;
	}
	
	public final function getPath() {
		return $this->m_sPath;
	}
	
	public final function getParameter($sName) {
		return $this->m_aParams[$sName];
	}
	
	public final function getAnchor() {
		return $this->m_sAnchor;
	}
	
	public function toString() {
		$sReturn = "{$this->m_sScheme}://";
		if($this->hasAuthentication()) {
			$sReturn .= "{$this->m_sUser}:{$this->m_sPass}@";
		}
		$sReturn .= $this->m_sHost;
		if($this->m_nPort != 80) {
			$sReturn .= ":{$this->m_nPort}";
		}
		$sReturn .= $this->m_sPath;
		if($this->hasParameters()) {
			$sReturn .= '?' . http_build_query($this->m_aParams, null, '&');
		}
		if($this->hasAnchor()) {
			$sReturn .= "#{$this->m_sAnchor}";
		}
		return $sReturn;
	}
}

?>