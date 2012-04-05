<?php

class OAuthClient {

	private $m_oConsumer;
	private $m_oProvider;
	private $m_oAccessToken;
	private $m_oRequestToken;

	public function __construct(OAuthProvider $oProvider, OAuthConsumer $oConsumer, OAuthToken $oRequestToken = null, OAuthToken $oAccessToken = null) {
		$this->m_oProvider = $oProvider;
		$this->m_oConsumer = $oConsumer;
		$this->m_oAccessToken = $oAccessToken;
		$this->m_oRequestToken = $oRequestToken;
	}

	public function getAccessToken() {
		return $this->m_oAccessToken;
	}
	
	public function getRequestToken() {
		return $this->m_oRequestToken;
	}
	
	public function getConsumer() {
		return $this->m_oConsumer;
	}

	public function getProvider() {
		return $this->m_oProvider;
	}

	public function isAuthorized() {
		return $this->getAccessToken() !== null;
	}

	public function requestAuthorization(array $aParams = array()) {
		$oRequest = $this->createRequest(OAuth::PROVIDER_REQUEST_TOKEN);
		$oRequest->setParameters($aParams);
		$aOAuth = OAuthUtil::parse_parameters($this->send($oRequest));
		if(isset($aOAuth['oauth_token']) && isset($aOAuth['oauth_token_secret']) && isset($aOAuth['oauth_callback_confirmed']) && $aOAuth['oauth_callback_confirmed']) {
			$this->m_oRequestToken = new OAuthToken($aOAuth['oauth_token'], $aOAuth['oauth_token_secret']);
		}
	}

	public function getAuthorizationUrl(array $aParams = array()) {
		if($this->getRequestToken() === null) {
			$this->requestAuthorization($aParams);
		}
		return $this->getProvider()->getUrl(OAuth::PROVIDER_AUTHENTICATE) . '?oauth_token=' . $this->getRequestToken()->getKey();
	}

	public function authorize(array $aParams = array()) {
		if($this->m_oRequestToken &&
			isset($_GET['oauth_token']) &&
			isset($_GET['oauth_verifier']) &&
			$_GET['oauth_token'] === $this->m_oRequestToken->getKey()
		) {
			$oRequest = $this->createRequest(OAuth::PROVIDER_ACCESS_TOKEN, null, null, $this->getRequestToken());
			$oRequest->setParameter('oauth_verifier', $_GET['oauth_verifier']);
			$oRequest->setParameters($aParams);
			$aOAuth = OAuthUtil::parse_parameters($this->send($oRequest, null, null, $this->getRequestToken()));
			if(isset($aOAuth['oauth_token']) && isset($aOAuth['oauth_token_secret'])) {
				$this->m_oAccessToken = new OAuthToken($aOAuth['oauth_token'], $aOAuth['oauth_token_secret']);
				$this->m_oRequestToken = null;
				return true;
			}
		}
		return false;
	}

	public function api($sUrl, $sMethod = 'GET', array $aParams = array()) {
		$oRequest = $this->createRequest(OAuth::PROVIDER_API, $sUrl, $sMethod, $this->getAccessToken());
		$oRequest->setParameters($aParams);
		return $this->send($oRequest, $this->getAccessToken());
	}

	private function createRequest($nType, $sUrlAppend = null, $sMethodOverwrite = null, OAuthToken $oToken = null) {
		$aParams = array(
			'oauth_version' 		=> OAuthUtil::getVersion(),
		    'oauth_nonce' 			=> OAuthUtil::generateNonce(),
		    'oauth_timestamp' 		=> OAuthUtil::generateTimestamp(),
		    'oauth_consumer_key' 	=> $this->getConsumer()->getKey()
		);
		if($oToken)
			$aParams['oauth_token'] = $oToken->getKey();

		return new OAuthRequest($this->getProvider()->getUrl($nType) . $sUrlAppend, $sMethodOverwrite === null ? $this->getProvider()->getMethod($nType) : $sMethodOverwrite, $aParams);
	}

	private function send(OAuthRequest $oRequest, OAuthToken $oToken = null) {
		$oSignatureMethod = null;
		if($this->getProvider()->getSignatureMethod() === 'HMAC-SHA1')
			$oSignatureMethod = new OAuthSignatureMethod_HMAC_SHA1();
		if($this->getProvider()->getSignatureMethod() === 'RSA-SHA1')
			$oSignatureMethod = new OAuthSignatureMethod_PLAINTEXT();
		if($this->getProvider()->getSignatureMethod() === 'PLAINTEXT')
			$oSignatureMethod = new OAuthSignatureMethod_RSA_SHA1();
		$oRequest->signRequest($oSignatureMethod, $this->m_oConsumer, $oToken);
		$oWebRequest = new WebRequest($oRequest->getNormalizedUrl(), $oRequest->getNormalizedMethod());
		$oWebRequest->addHeaders($oRequest->getHeaders());
		$oWebRequest->addFields($oRequest->getFields());
		$oWebResponse = $oWebRequest->send();
		
		if($oWebResponse->getHttpCode() !== 200) {
			// TODO: throw something !
		}
		
		return $oWebResponse->getContent();
	}

	private function tryLoadTokensFromGet() {
		if(isset($_GET['oauth_token']) && isset($_GET['oauth_verifier'])) {
			$this->m_oToken = new OAuthToken($_GET['oauth_token'], $_GET['oauth_verifier']);
			return true;
		}
		return false;
	}

	/**
	 * attempt to build up a request from what was passed to the server
	 */
	public static function from_request($http_method=NULL, $http_url=NULL, $parameters=NULL) {
		$scheme = (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != "on")
		? 'http'
		: 'https';
		$http_url = ($http_url) ? $http_url : $scheme .
                              '://' . $_SERVER['SERVER_NAME'] .
                              ':' .
		$_SERVER['SERVER_PORT'] .
		$_SERVER['REQUEST_URI'];
		$http_method = ($http_method) ? $http_method : $_SERVER['REQUEST_METHOD'];

		// We weren't handed any parameters, so let's find the ones relevant to
		// this request.
		// If you run XML-RPC or similar you should use this to provide your own
		// parsed parameter-list
		if (!$parameters) {
			// Find request headers
			$request_headers = OAuthUtil::get_headers();

			// Parse the query-string to find GET parameters
			$parameters = OAuthUtil::parse_parameters($_SERVER['QUERY_STRING']);

			// It's a POST request of the proper content-type, so parse POST
			// parameters and add those overriding any duplicates from GET
			if ($http_method == "POST"
			&&  isset($request_headers['Content-Type'])
			&& strstr($request_headers['Content-Type'],
                     'application/x-www-form-urlencoded')
			) {
				$post_data = OAuthUtil::parse_parameters(
				file_get_contents(self::$POST_INPUT)
				);
				$parameters = array_merge($parameters, $post_data);
			}

			// We have a Authorization-header with OAuth data. Parse the header
			// and add those overriding any duplicates from GET or POST
			if (isset($request_headers['Authorization']) && substr($request_headers['Authorization'], 0, 6) == 'OAuth ') {
				$header_parameters = OAuthUtil::split_header(
				$request_headers['Authorization']
				);
				$parameters = array_merge($parameters, $header_parameters);
			}

		}

		return new OAuthRequest($http_method, $http_url, $parameters);
	}
}

?>