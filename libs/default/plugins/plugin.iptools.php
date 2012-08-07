<?php

class IpTools extends Plugin {

	private $m_nIpToHostExpiration;
	private $m_nHostToIpsExpiration;
	private $m_nHostToDnsExpiration;
	private $m_nGeoLocalisationExpiration;
	private $m_sGeoLocalisationUrl;
	
	private static $s_oSingleton;
	
	public function make(array $aMembers) {
		$this->m_nIpToHostExpiration = $this->getConfig('IPTOHOST_EXPIRATION', 300);
		$this->m_nHostToIpsExpiration = $this->getConfig('HOSTTOIPS_EXPIRATION', 300);
		$this->m_nHostToDnsExpiration = $this->getConfig('HOSTTODNS_EXPIRATION', 300);
		$this->m_nGeoLocalisationExpiration = $this->getConfig('GEOLOCALISATION_EXPIRATION', 300);
		$this->m_sGeoLocalisationUrl = $this->getConfig('GEOLOCALISATION_URL', null);
	}
	
	public function init() {
		self::$s_oSingleton = $this;
	}
	
	private function _getGeoData($sIp) {
		return parent::getWatena()->getCache()->retrieve('W_IPTOOLS_GEO_'.$sIp, create_function('$sIp', '
			$oRequest = new WebRequest($sIp);
			$oResponse = $oRequest->send();
			return json_decode($oResponse->getContent());
		'), $this->m_nGeoLocalisationExpiration, array($this->m_sGeoLocalisationUrl . $sIp));
	}
	
	public static function getIpsByHost($sHost) {
		$sHost = Encoding::toLower($sHost);
		return parent::getWatena()->getCache()->retrieve('W_IPTOOLS_IPSBYHOST_'.$sHost, 'gethostbynamel', self::$s_oSingleton->m_nIpToHostExpiration, array($sHost));
	}
	
	public static function getHostByIp($sIp) {
		return parent::getWatena()->getCache()->retrieve('W_IPTOOLS_HOSTBYIP_'.$sIp, 'gethostbyaddr', self::$s_oSingleton->m_nHostToIpsExpiration, array($sIp));
	}
	
	public static function getDnsByHost($sHost) {
		$sHost = Encoding::toLower($sHost);
		return parent::getWatena()->getCache()->retrieve('W_IPTOOLS_DNSBYHOST_'.$sHost, 'dns_get_record', self::$s_oSingleton->m_nHostToDnsExpiration, array($sHost));
	}
	
	public static function getDnsByIp($sIp) {
		return self::getDnsByHost(self::getHostByIp($sIp));
	}
	
	public static function getGeoCountryName($sIp) {
		return self::$s_oSingleton->_getGeoData($sIp)->country_name;
	}
	
	public static function getGeoCountryCode($sIp) {
		return self::$s_oSingleton->_getGeoData($sIp)->country_code;
	}
	
	public static function getGeoCity($sIp) {
		return self::$s_oSingleton->_getGeoData($sIp)->city;
	}
	
	/**
	 * Retrieve version information of this plugin.
	 * The format is an associative array as follows:
	 * 'major' => Major version (int)
	 * 'minor' => Minor version (int)
	 * 'build' => Build version (int)
	 * 'state' => Naming of the production state
	 */
	public function getVersion() {
		return array(
			'major' => 0,
			'minor' => 1,
			'build' => 1,
			'state' => 'dev'
		);
	}
}

?>