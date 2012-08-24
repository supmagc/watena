<?php

/**
 * Class that provides the data that are uwed when requesting some ajax-data
 *
 * @author Voet Jelle - ToMo-design
 * @version 2.0.1 beta
 * 
 * VERSION-LOG
 * -----------
 * 
 * 1-8-2010: 2.0.0 => 2.0.1
 * - Made the generated javascript-function call a bit smaller
 * - Fixed a possible bug when using a variable URL
 * 
 * 30-7-2010: 1.0.0 => 2.0.0
 * - Revamped the processed output
 * - Added JSON
 * - Added rawurlencode/encodeURIComponent
 * - Made the output somewhat smaller
 */
class AJAX_Request extends Object {
	
	private $m_sUrl = null;
	private $m_sFunction = null;
	private $m_sCallback = null;
	private $m_nArgCount = 0;
	private $m_aValues = array();
	
	/**
	 * Create a new request
	 *
	 * @param string $sURL the request URI
	 * @param mixed $PHPCallback the php function to be called on the server to process the request
	 * @param string $sJSCallback the javascript function that you can call on your html-page
	 * @param int $nArgCount the number of arguments you can set in your javascript-/php-function
	 */
	public function __construct($sFunction, $nArgCount = 0) {
		$this->m_sFunction = $sFunction;
		$this->m_nArgCount = $nArgCount;
	}
	
	public function getFunction() {
		return $this->m_sFunction;
	}
	
	/**
	 * Set a fixed url that will be called when the function is invoked.
	 * 
	 * @param string $sUrl
	 */
	public function setUrl($sUrl) {
		$this->m_sUrl = '' . $sUrl;
	}
	
	public function getUrl() {
		return $this->m_sUrl;
	}
	
	public function setCallback($sCallback) {
		$this->m_sCallback = $sCallback;
	}
	
	public function getCallback() {
		return $this->m_sCallback;
	}
	
	/**
	 * Set a static value to be processed by this request
	 *
	 * @param string $sName
	 * @param string $sValue
	 */
	public function setValue($sName, $sValue) {
		if(Encoding::regMatch('/^[-a-z0-9_]+$/i', $sName)) {
			$this->m_aValues[$sName] = $sValue;
		}
		else {
			$this->getLogger()->warning('AJAX_Request invalid value-name \'{name}\'.', array($sName));
		}
	}
	
	/**
	 * Set the number of arguments you can set for your javascript-/php-function
	 *
	 * @param int $nArgCount
	 */
	public function setArgCount($nArgCount) {
		$this->m_nArgCount = $nArgCount;
	}
	
	/**
	 * Process this request-data
	 *
	 * @return string
	 */
	public function getOutput() {
		$sRet = '';
		$sRet .= 'function ' . $this->getFunction() . '(';
		if(!$this->getUrl()) $sRet .= 'sUrl' . ($this->m_nArgCount > 0 ? ', ' : '');
		for($i=0 ; $i<$this->m_nArgCount ; ++$i) {
			$sRet .= "_arg$i" . (($i + 1) < $this->m_nArgCount ? ', ' : '');
		}
		$sRet .= ") {";
		$sRet .= "TMX_Send(";
		if($this->getUrl()) $sRet .= '(sUrl.length > 0 ? sUrl : "' . rawurlencode($this->getUrl()) . '")';
		else $sRet .= '"' . rawurlencode($this->getUrl()) . '"';
		$sRet .= ", \"" . $this->getCallback() . "\", [";
		for($i=0 ; $i<$this->m_nArgCount ; ++$i) {
			$sRet .= ($i > 0 ? ', ' : '') . "_arg$i";
		}
		$sRet .= "], \"" . rawurlencode(json_encode($this->m_aValues)) . "\");";
		$sRet .= "}\n";
		
		return $sRet;
	}
}
?>