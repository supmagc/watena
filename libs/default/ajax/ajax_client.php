<?php

/**
 * Client TMX class that processes all registered requests
 *
 * @author Voet Jelle - ToMo-design
 * @version 1.0.0 beta
 */
class AJAX_Client {
	
	private $m_aRequests = array();
	private $m_sPrefix = 'TMX';
	private $m_sJSFile = 'TMX.js';
	private $m_bProcessed = false;
	
	/**
	 * Create a new client instance
	 *
	 * @param string $sJSFile tha file with all the TMX-javascript code
	 * @param string $sPrefix the prefix that will be used on the JS-funcrions
	 */
	public function __construct($sJSFile, $sPrefix = 'TMX') {
		$this->m_sPrefix = $sPrefix;
		$this->m_sJSFile = $sJSFile;
	}
	
	/**
	 * Register a new request
	 *
	 * @param TMX_Request $oRequest
	 */
	public function registerRequest(AJAX_Request $oRequest) {
		if(!in_array($oRequest, $this->m_aRequests)) $this->m_aRequests []= $oRequest;
	}
	
	/**
	 * Process all registered requests
	 *
	 * @param bool $bEcho choose if all data should be auto-echo-ed
	 * @return string
	 */
	public function process($bEcho = true) {
		if(!$this->m_bProcessed) $this->m_bProcessed = true;
		else echo '<-- Unable to process the same TMX instance a second time -->';

		$sRet = '';
		if($bEcho) echo '<script language="javascript 1.8" type="text/javascript" src="'.$this->m_sJSFile.'"></script><script language="javascript 1.8" type="text/javascript">TMX_sPrefix = "'.$this->m_sPrefix.'"' . "\n";
		else $sRet .= '<script language="javascript 1.8" type="text/javascript" src="'.$this->m_sJSFile.'"></script><script language="javascript 1.8" type="text/javascript">TMX_sPrefix = "'.$this->m_sPrefix.'"' . "\n";
		foreach($this->m_aRequests as $oReq) {
			if($bEcho) $oReq->Process(true);
			$sRet .= $oReq->Process(false);
		}
		if($bEcho) echo '</script>';
		else $sRet .= '</script>';
		
		return $sRet;
	}
}
?>