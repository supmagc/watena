<?php namespace Watena\Core;

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
		$aData = parse_url('' . $sUrl);
		if(is_array($aData)) {
			$this->m_sScheme = !empty($aData['scheme']) ? $aData['scheme'] : 'http';
			$this->m_sUser = !empty($aData['user']) ? $aData['user'] : null;
			$this->m_sPass = !empty($aData['pass']) ? $aData['pass'] : null;
			$this->m_sHost = !empty($aData['host']) ? $aData['host'] : 'localhost';
			$this->m_nPort = !empty($aData['port']) ? (int)$aData['port'] : 80;
			$this->m_sPath = !empty($aData['path']) ? $aData['path'] : '/';
			$this->m_aParams = !empty($aData['query']) ? $aData['query'] : '';
			$this->m_sAnchor = !empty($aData['fragment']) ? $aData['fragment'] : null;
			parse_str($this->m_aParams, $this->m_aParams);
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
	
	public final function setScheme($sScheme) {
		$this->m_sScheme = Encoding::regReplace('[^a-zA-Z]', '', $sScheme);
	}
	
	public final function getScheme() {
		return $this->m_sScheme;
	}
	
	public final function setUserName($sUserName) {
		$this->m_sUser = $sUserName;
	}
	
	public final function getUserName() {
		return $this->m_sUser;
	}
	
	public final function setPassword($sPassword) {
		$this->m_sPass = $sPassword;
	}
	
	public final function getPassword() {
		return $this->m_sPass;
	}
	
	public final function setHost($sHost) {
		$this->m_sHost = $sHost;
	}
	
	public final function getHost() {
		return $this->m_sHost;
	}
	
	public final function setPort($nPort) {
		$this->m_nPort = max(1, (int)$nPort);
	}
	
	public final function getPort() {
		return $this->m_nPort;
	}
	
	public final function setPath($sPath) {
		if(!Encoding::beginsWith($sPath, '/')) {
			$sPath = "/$sPath";
		}
		return $this->m_sPath = $sPath;
	}
	
	public final function getPath() {
		return $this->m_sPath;
	}
	
	public final function addParameter($sName, $mValue) {
		$this->m_aParams[$sName] = $mValue;
	}
	
	public final function addParameters(array $aParams) {
		$this->m_aParams = array_merge($this->m_aParams, $aParams);
	}
	
	public final function setParameters(array $aParams) {
		$this->m_aParams = $aParams;
	}
	
	public final function getParameter($sName) {
		return $this->m_aParams[$sName];
	}
	
	public final function getParameters() {
		return $this->m_aParams;
	}
	
	public final function setAnchor($sAnchor) {
		$this->m_sAnchor = $sAnchor;
	}
	
	public final function getAnchor() {
		return $this->m_sAnchor;
	}
	
	public function toString() {
		$sReturn = "{$this->m_sScheme}://";
		if($this->hasAuthentication()) {
			$sReturn .= rawurlencode($this->m_sUser) . ':' . rawurlencode($this->m_sPass) . '@';
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
			$sReturn .= '#' . rawurlencode($this->m_sAnchor);
		}
		return $sReturn;
	}
}
