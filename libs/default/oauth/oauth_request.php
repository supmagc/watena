<?php
class OAuthRequest {
	
	protected $m_aParameters;
	protected $m_sMethod;
	protected $m_sUrl;
	
	// for debug purposes
	public $base_string;
	public static $version = '1.0';
	public static $POST_INPUT = 'php://input';

	function __construct($sUrl, $sMethod, array $aParameters) {
		$aParameters = array_merge(OAuthUtil::parse_parameters(parse_url($sUrl, PHP_URL_QUERY)), $aParameters);
		$this->m_aParameters = $aParameters;
		$this->m_sMethod = $sMethod;
		$this->m_sUrl = $sUrl;
	}
	
	public function getUrl() {
		return $this->m_sUrl;
	}
	
	public function getMethod() {
		return $this->m_sMethod;
	}

	public function setParameter($sName, $sValue) {
		$this->m_aParameters[$sName] = $sValue;
	}

	public function setParameters(array $aParams) {
		$this->m_aParameters = array_merge($this->m_aParameters, $aParams);
	}
	
	public function getParameter($sName) {
		return isset($this->m_aParameters[$sName]) ? $this->m_aParameters[$sName] : null;
	}

	public function getParameters() {
		return $this->m_aParameters;
	}

	public function unsetParameter($sName) {
		unset($this->m_aParameters[$sName]);
	}

	/**
	 * The request parameters, sorted and concatenated into a normalized string.
	 * 
	 * @return string
	 */
	public function getSignableParameters() {
		// Grab all parameters
		$aParams = $this->m_aParameters;

		// Remove oauth_signature if present
		// Ref: Spec: 9.1.1 ("The oauth_signature parameter MUST be excluded.")
		if(isset($aParams['oauth_signature'])) {
			unset($aParams['oauth_signature']);
		}
		
		return OAuthUtil::buildHttpQuery($aParams);
	}

	/**
	 * Returns the base string of this request.
	 * The base string defined as the method, the url
	 * and the parameters (normalized), each urlencoded
	 * and the concated with &.
	 */
	public function getSignatureBasestring() {
		$aParts = array(
			$this->getNormalizedMethod(),
			$this->getNormalizedUrl(),
			$this->getSignableParameters()
		);

		$aParts = OAuthUtil::urlencode_rfc3986($aParts);

		return implode('&', $aParts);
	}

	/**
	 * just uppercases the http method
	 */
	public function getNormalizedMethod() {
		return strtoupper($this->m_sMethod);
	}

	/**
	 * parses the url and rebuilds it to be
	 * scheme://host/path
	 */
	public function getNormalizedUrl() {
		$aParts = parse_url($this->m_sUrl);

		$sScheme = (isset($aParts['scheme'])) ? $aParts['scheme'] : 'http';
		$sPort = (isset($aParts['port'])) ? $aParts['port'] : (($sScheme == 'https') ? '443' : '80');
		$sHost = (isset($aParts['host'])) ? strtolower($aParts['host']) : '';
		$sPath = (isset($aParts['path'])) ? $aParts['path'] : '';

		if (($sScheme == 'https' && $sPort != '443')
		|| ($sScheme == 'http' && $sPort != '80')) {
			$sHost = "$sHost:$sPort";
		}
		return "$sScheme://$sHost$sPath";
	}
	
	public function getFields() {
		$aFields = array();
		foreach($this->m_aParameters as $sKey => $mValue) {
			if(strpos($sKey, 'oauth_') !== 0)
				$aFields[$sKey] = $mValue;
		}
		return $aFields;
	}
	
	public function getHeaders() {
		$aOAuths = array();
		foreach($this->m_aParameters as $sKey => $mValue) {
			if(strpos($sKey, 'oauth_') === 0)
				$aOAuths []= urlencode($sKey) . '="' . OAuthUtil::urlencode_rfc3986($mValue) . '"';
		}
		return array('Authorization' => 'OAuth ' . implode(', ', $aOAuths));
	}

	public function signRequest(OAuthSignatureMethod $oSignatureMethod, OAuthConsumer $oConsumer, OAuthToken $oToken = null) {
		$this->setParameter("oauth_signature_method", $oSignatureMethod->getName(), false);
		$sSignature = $this->buildSignature($oSignatureMethod, $oConsumer, $oToken);
		$this->setParameter("oauth_signature", $sSignature, false);
	}

	public function buildSignature(OAuthSignatureMethod $oSignatureMethod, OAuthConsumer $oConsumer, OAuthToken $oToken = null) {
		return $oSignatureMethod->buildSignature($this, $oConsumer, $oToken);
	}
	
	public function __toString() {
		return "OAuthRequest[url={$this->getUrl()},method={$this->getMethod()}]";
	}
}
?>