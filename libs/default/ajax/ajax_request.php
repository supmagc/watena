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
	
	private $m_sPath;
	private $m_sTrigger;
	private $m_sCallback;
	private $m_aValues = array();
	
	/**
	 * Create a new request
	 *
	 * @param string $sURL the request URI
	 * @param mixed $PHPCallback the php function to be called on the server to process the request
	 * @param string $sJSCallback the javascript function that you can call on your html-page
	 * @param int $nArgCount the number of arguments you can set in your javascript-/php-function
	 */
	public function __construct($sPath, $sFunction) {
		$this->m_sPath = $sPath;
		$this->m_sTrigger = $sFunction;
		$this->m_sCallback = $sFunction;
	}
	
	public function getPath() {
		return $this->m_sPath;
	}
	
	public function getJavascriptTrigger() {
		return $this->m_sTrigger;
	}
	
	public function getPhpCallback() {
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
	 * Process this request-data
	 *
	 * @return string
	 */
	public function getOutput() {
		$sRet = "function {$this->getJavascriptTrigger()}() {AJAX(";
		$sRet .= "'" . rawurlencode(Request::make($this->getPath())->toString()) . "', ";
		$sRet .= "'" . $this->getPhpCallback() . "', ";
		$sRet .= "arguments, '" . rawurlencode(json_encode($this->m_aValues)) . "'";
		$sRet .= ");}";
		return $sRet;
	}
}
?>