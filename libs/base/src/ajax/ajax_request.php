<?php namespace Watena\Libs\Base;

/**
 * Class that provides the data that are uwed when requesting some ajax-data
 *
 * @author Voet Jelle - ToMo-design
 * @version 2.0.2 beta
 * 
 * CHANGELOG
 * ---------
 * 
 * 6-5-2015: 2.0.1 => 2.0.2
 * - Documentation update
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
	private $m_sMethod;
	private $m_aValues = array();
	
	/**
	 * Create a new request
	 *
	 * @param string $sURL the request URI
	 * @param string $sFunction The function of the callback (used as javascript functioname, and php method)
	 */
	public function __construct($sPath, $sFunction) {
		$this->m_sPath = $sPath;
		$this->m_sTrigger = $sFunction;
		$this->m_sMethod = $sFunction;
	}
	
	public function getPath() {
		return $this->m_sPath;
	}
	
	public function getJavascriptTrigger() {
		return $this->m_sTrigger;
	}
	
	public function getPhpMethod() {
		return $this->m_sMethod;
	}
	
	/**
	 * Process this request-data
	 *
	 * @return string
	 */
	public function getOutput($bDebug) {
		$sRet = "function {$this->getJavascriptTrigger()}() {AJAX(";
		$sRet .= "'" . rawurlencode(Request::make($this->getPath())->toString()) . "', ";
		$sRet .= "'" . $this->getPhpMethod() . "', ";
		$sRet .= "arguments, " . ($bDebug ? '1' : '0');
		$sRet .= ");}";
		return $sRet;
	}
}
