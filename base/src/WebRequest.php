<?php namespace Watena\Core;

/**
 * Create a request to another server.
 * Internally all settings are translated to CURL options.
 * 
 * If the given Url has some parameters and you choose to use the POST method,
 * you'll have to add your post-fields seperatly, as the Url-Parameters will 
 * still be send as GET. If not using post-data, the WebRequest-Fields will be
 * appended to the full Url.
 * 
 * At the moment it is only possible to add duplicate parameter names for POST 
 * request, by using the setPostDataAppend()
 * 
 * Update Notes:
 * 0.2.1 25/10/2014
 * - The Url instance gets cloed so external adjustments don't reflect internally
 * - Made it possible to change the ssl-certificate for https.
 * 
 * 0.2.0 18/10/2014
 * - Changed the internal working to use the url class
 * - Added the appendPostData() functions (needed for closure minification api call)
 * - A lot of the parameters should now be set by adjusting the Url class
 * 
 * 0.1.0
 * - Initial version
 * 
 * @author Jelle Voet
 * @version 0.2.0
 */
class WebRequest extends Object {
	
	private $m_oUrl = null;
	private $m_oCurl = null;
	private $m_sMethod = 'GET';
	private $m_sPostDataAppend = null;
	private $m_sUserAgent = 'watena-curl';
	private $m_sSslCertificate = 'D:ca-certificates/cacert.pem';
	private $m_aFields = array();
	private $m_aHeaders = array();
	private $m_aCookies = array();

	/**
	 * Some default curl options that are required when using this wrapper.
	 * 
	 * @var array
	 */
	public static $OPTIONS_DEFAULT = array(
		CURLOPT_HEADER 			=> true,
		CURLOPT_RETURNTRANSFER 	=> true,
		CURLOPT_FOLLOWLOCATION	=> true,
		CURLOPT_CONNECTTIMEOUT 	=> 10,
		CURLOPT_TIMEOUT			=> 60,
		CURLOPT_HTTP_VERSION	=> CURL_HTTP_VERSION_1_1
	);
	
	/**
	 * Create a new WebRequest-instance, by providing the Url and send method.
	 * The Url-reference itself cannot be changed later on, but its parameters can be accessed.
	 * The Url-object will be cloned, so any modifications you'll make to the original
	 * Url-instance will not reflect on the Url used for this WebRequest.
	 * 
	 * @param Url $oUrl
	 * @param string $sMethod
	 */
	public final function __construct(Url $oUrl, $sMethod = 'GET') {
		$this->m_oUrl = clone $oUrl;
		$this->m_sMethod = $sMethod;
		
		$this->m_oCurl = curl_init();
	}

	/**
	 * Destroys the Url object and releases it's curl-resources.
	 */
	public final function __destruct() {
		curl_close($this->m_oCurl);
	}

	/**
	 * Get the internal curl-resource handle.
	 * 
	 * @return resource
	 */
	public final function getCurl() {
		return $this->m_oCurl;
	}

	/**
	 * Get the remote endpoint in the form of an Url-instance.
	 * 
	 * @return Url
	 */
	public final function getUrl() {
		return $this->m_oUrl;
	}

	/**
	 * Set the HTTP-method used for the request.
	 * Popular choice are GET, POST (or maybe PUT, DELETE, ...)
	 * 
	 * @param string $sMethod
	 */
	public final function setMethod($sMethod) {
		$this->m_sMethod = $sMethod;
	}
	
	/**
	 * Get the HTTP-method for the request.
	 * Default will be 'GET', unless specified differently during construction,
	 * or by an earlier call to setMethod()
	 * 
	 * @return string
	 */
	public final function getMethod() {
		return $this->m_sMethod;
	}

	/**
	 * Set the UserAgent from the request.
	 * 
	 * @param string $sUserAgent
	 */
	public final function setUseragent($sUserAgent) {
		$this->m_sUserAgent = $sUserAgent;
	}

	/**
	 * Get the UserAgent from the request.
	 * Default will be 'watena-curl'
	 * 
	 * @return string
	 */
	public final function getUserAgent() {
		return $this->m_sUserAgent;
	}
	
	/**
	 * Set the path to the ssl-certificate for the request
	 * This can be a watena-path, but the verification is done once the certificate is required.
	 * 
	 * @param string $sSslCertificate
	 */
	public final function setSslCertificate($sSslCertificate) {
		$this->m_sSslCertificate = $sSslCertificate;
	}

	/**
	 * Get teh ssl-certificate path from the request.
	 * 
	 * @return string
	 */
	public final function getSslCertificate() {
		return $this->m_sSslCertificate;
	}
	
	/**
	 * Get the post-data append string for the request.
	 * This property is only used when getMethod() == 'POST'
	 * and adds additional raw data to the request.
	 * 
	 * Make sure you correctly encode the string!
	 * This makes it possible to send duplicate variables in the same request.
	 * 
	 * @param string $sPostDataAppend
	 */
	public final function setPostDataAppend($sPostDataAppend) {
		$this->m_sPostDataAppend = $sPostDataAppend;
	}

	/**
	 * Get the post-data from the request.
	 * 
	 * @return string
	 */
	public final function getPostDataAppend() {
		return $this->m_sPostDataAppend;
	}

	/**
	 * Add a custom field for the request.
	 * These fields might be added to the parameters of the Url when getMethod() != 'POST'
	 * 
	 * @param string $sKey
	 * @param mixed $mValue
	 */
	public final function addField($sKey, $mValue) {
		$this->m_aFields['' . $sKey] = $mValue;
	}

	/**
	 * Add an array with key => values pairs as fields to the request.
	 * 
	 * @see addField()
	 * @param array $aFields
	 */
	public final function addFields(array $aFields) {
		$this->m_aFields = array_merge($this->m_aFields, $aFields);
	}
	
	/**
	 * Get an associative array with all the dedicated fields from the request.
	 * The data returned is not the sameg (or merged) as getUrl()->getParameters()
	 * 
	 * @return array
	 */
	public final function getFields() {
		return $this->m_aFields;
	}
	
	/**
	 * Get the fields data from the request in a valid string format.
	 * The data returned is parsable by parse_str(), and can be used as post data.
	 * 
	 * @see getFields()
	 * @return string
	 */
	public final function getFieldsAsString() {
		return http_build_query($this->m_aFields, null, '&');
	} 

	/**
	 * Add a custom header to the request.
	 * Most default headers are supported, but you still might want to set a custom header.
	 * 
	 * @param string $sKey
	 * @param mixed $mValue
	 */
	public final function addHeader($sKey, $mValue) {
		$this->m_aHeaders['' . $sKey] = $mValue;
	}

	/**
	 * Add an array with key => value headers to the request.
	 * 
	 * @see addHeader()
	 * @param array $aHeaders
	 */
	public final function addHeaders(array $aHeaders) {
		$this->m_aHeaders = array_merge($this->m_aHeaders, $aHeaders);
	}
	
	/**
	 * Get an associative array with all the additional headers from the request.
	 * 
	 * @return array
	 */
	public final function getHeaders() {
		return $this->m_aHeaders;
	}
	
	/**
	 * Get a flat/none-associative array with all the aditional headers from the request.
	 * 
	 * @return array
	 */
	public final function getHeadersAsList() {
		return array_map(create_function('$a, $b', 'return $a.\': \'.$b;'), array_keys($this->m_aHeaders), array_values($this->m_aHeaders));
	}

	/**
	 * Get a string containing all the additional headers from the request.
	 * The returned string is properly formatted with \r\n linebreaks.
	 * 
	 * @return string
	 */
	public final function getHeadersAsString() {
		return implode("\r\n", $this->getHeadersAsList());
	}
	
	/**
	 * Add a temporary cookie for the request.
	 * 
	 * @param string $sKey
	 * @param mixed $mValue
	 */
	public final function addCookie($sKey, $mValue) {
		$this->m_aCookies['' . $sKey] = $mValue;
	}
	
	/**
	 * Add an array with key => value temporary cookies to the request.
	 * 
	 * @see addCookie()
	 * @param array $aCookies
	 */
	public final function addCookies(array $aCookies) {
		$this->m_aCookies = array_merge($this->m_aCookies, $aCookies);
	}
	
	/**
	 * Get an associative array with all the temporary cookies from the request.
	 * 
	 * @return array
	 */
	public final function getCookies() {
		return $this->m_aCookies;
	}

	/**
	 * Get a string containing the temporary cookies from the request.
	 * The string is properly formatted to be send as header. 
	 * 
	 * @return string
	 */
	public final function getCookiesAsString() {
		return http_build_query($this->m_aCookies, null, '; ');
	}
	
	/**
	 * Copy the cookies from the current request to the new WebRequest
	 * 
	 * @see addCookies()
	 */
	public final function takeCookies() {
		$this->addCookies($_COOKIE);
	}

	/**
	 * Get an array containing all the curl-options that will be used when calling send()
	 * 
	 * @return array
	 */
	public final function getCurlOptions() {
		$aOptions = self::$OPTIONS_DEFAULT;
		
		if($this->m_oUrl->getUserName() && $this->m_oUrl->getPassword()) {
			$aOptions[CURLOPT_USERPWD] = urlencode($this->m_oUrl->getUserName()) . ':' . urlencode($this->m_oUrl->getPassword());
		}
		
		if($this->m_oUrl->getScheme() == 'https') {
			$sPath = $this->getWatena()->getPath($this->m_sSslCertificate);
			if(!$sPath) {
				$this->getLogger()->warning('Unable to detect a valid SslCertificate-file at {sslcertificate} while using https for {url}', array('url' => $this->m_oUrl->toString(), 'sslsertificate' => $this->m_sSslCertificate));
			}
			$aOptions[CURLOPT_CAINFO] = $sPath;
			$aOptions[CURLOPT_SSL_VERIFYPEER] = true;
			$aOptions[CURLOPT_SSL_VERIFYHOST] = 2;
		}
		
		$aOptions[CURLOPT_USERAGENT] = $this->m_sUserAgent;
		$aOptions[CURLOPT_HTTPHEADER] = $this->getHeadersAsList();
		$aOptions[CURLOPT_COOKIE] = $this->getCookiesAsString();
		
		$oUrl = clone $this->m_oUrl;
		if($this->m_sMethod == 'POST') {
			$aOptions[CURLOPT_POST] = true;
			$aOptions[CURLOPT_POSTFIELDS] = $this->getFieldsAsString();
			if($this->m_sPostDataAppend) {
				$aOptions[CURLOPT_POSTFIELDS] .= ($aOptions[CURLOPT_POSTFIELDS] ? '&' : '') . $this->m_sPostDataAppend;
			}
		}
		else {
			if($this->m_sMethod == 'PUT')
				$aOptions[CURLOPT_PUT] = true;
			else if($this->m_sMethod == 'GET')
				$aOptions[CURLOPT_HTTPGET] = true;
			else
				$aOptions[CURLOPT_CUSTOMREQUEST] = $this->m_sMethod;
			
			$oUrl->addParameters($this->getFields());
		}
		$aOptions[CURLOPT_URL] = $oUrl->toString();
		
		return $aOptions;
	}
	
	/**
	 * Try to actually send the request.
	 * 
	 * @throws WebException When no data was returned.
	 * @return WebResponse
	 */
	public final function send() {
		// Set options
		$aOptions = $this->getCurlOptions();
		curl_setopt_array($this->getCurl(), $aOptions);

		// Send request, and verify
		$nTime = microtime(true);
		$this->getLogger()->info('Starting to send a WebRequest to {url}', array('url' => $this->m_oUrl->toString(), 'options' => $aOptions));
		$mData = curl_exec($this->m_oCurl);
		$this->getLogger()->info('WebRequest to {url} completed after {time}sec.', array('url' => $this->m_oUrl->toString(), 'time' => (microtime(true) - $nTime)));
		if($mData === false) {
			throw new WebException($this);
		}
		
		// Pass along the curl object, and not the request since the
		// request can be changed later on and will thus no longer
		// represent the actual values used when sending.
		return new WebResponse($this->m_oCurl, $mData);
	}
}
