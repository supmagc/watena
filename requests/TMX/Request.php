<?php

/**
 * Class that provides the data that are uwed when requesting some TMX-data
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
class TMX_Request {
	
	private $m_sURL;
	private $m_PHPCallback;
	private $m_sJSCallback;
	private $m_nArgCount;
	private $m_aValues = array();
	private $m_bVariableURL = false;
	
	/**
	 * Create a new request
	 *
	 * @param string $sURL the request URI
	 * @param mixed $PHPCallback the php function to be called on the server to process the request
	 * @param string $sJSCallback the javascript function that you can call on your html-page
	 * @param int $nArgCount the number of arguments you can set in your javascript-/php-function
	 */
	public function __construct($sURL, $PHPCallback, $sJSCallback, $nArgCount = 0) {
		$this->m_sURL = $sURL;
		$this->m_PHPCallback = $PHPCallback;
		$this->m_sJSCallback = $sJSCallback;
		$this->m_nArgCount = $nArgCount;
	}
	
	/**
	 * If you make the request-URL variable the first parameter when you call the JS-function should be the URL to be called
	 * default = false
	 */
	public function MakeURLVariable() {
		$this->m_bVariableURL = true;
	}
	
	/**
	 * Set a static value to be processed by this request
	 *
	 * @param string $sName
	 * @param string $sValue
	 */
	public function SetValue($sName, $sValue) {
		if(preg_match('/^[-a-z0-9_]+$/i', $sName))
			$this->m_aValues[$sName] = $sValue;
		else
			trigger_error('Invalid value-name: ' . $sName);
	}
	
	/**
	 * Set the number of arguments you can set for your javascript-/php-function
	 *
	 * @param int $nArgCount
	 */
	public function SetArgCount($nArgCount) {
		$this->m_nArgCount = $nArgCount;
	}
	
	/**
	 * Process this request-data
	 *
	 * @param bool $bEcho auto-echo this request-data
	 * @return string
	 */
	public function Process($bEcho = true) {
		$sRet = '';
		$sRet .= 'function ' . $this->m_sJSCallback . '(';
		if($this->m_bVariableURL) $sRet .= 'sPUrl' . ($this->m_nArgCount > 0 ? ', ' : '');
		for($i=0 ; $i<$this->m_nArgCount ; ++$i) {
			$sRet .= "_arg$i" . (($i + 1) < $this->m_nArgCount ? ', ' : '');
		}
		$sRet .= ") {";
		$sRet .= "TMX_Send(";
		if($this->m_bVariableURL) $sRet .= '(sPUrl.length > 0 ? sPUrl : "' . rawurlencode($this->m_sURL) . '")';
		else $sRet .= '"' . rawurlencode($this->m_sURL) . '"';
		$sRet .= ", \"" . rawurlencode(serialize($this->m_PHPCallback)) . "\", [";
		for($i=0 ; $i<$this->m_nArgCount ; ++$i) {
			$sRet .= ($i > 0 ? ', ' : '') . "_arg$i";
		}
		$sRet .= "], \"" . rawurlencode(json_encode($this->m_aValues)) . "\");";
		$sRet .= "}\n";
		
		if($bEcho) echo $sRet;
		return $sRet;
	}
}
?>