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
	private $m_sJavascriptLink = 'TMX.js';
	
	/**
	 * Create a new client instance
	 *
	 * @param string $sJSFile tha file with all the TMX-javascript code
	 * @param string $sPrefix the prefix that will be used on the JS-funcrions
	 */
	public function __construct($sJavascriptLink, $sPrefix = 'TMX') {
		$this->m_sPrefix = $sPrefix;
		$this->m_sJavascriptLink = $sJavascriptLink;
	}
	
	/**
	 * Register a new request
	 *
	 * @param AJAX_Request $oRequest
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
	public function getOutput() {
		$sRet = <<<EOT
<script language="javascript 1.8" type="text/javascript"><!--
window['ajax] = window['ajax'] || (function(doc, tag, src, ele, tar) {
	ele = doc.createElement(tag);
	tar = doc.getElementsByTagName(tag)[0];
	ele.async = 1;
	ele.src = src;
	tar.parentNode.insertBefore(ele, tar)
})(document, 'script', '{$this->getJavascriptLink()}');
--></script>
EOT;
//		$sRet .= '<script language="javascript 1.8" type="text/javascript" src="'.$this->m_sJSFile.'"></script><script language="javascript 1.8" type="text/javascript">TMX_sPrefix = "'.$this->m_sPrefix.'"' . "\n";
		$sRet .= '<script language="javascript 1.8" type="text/javascript"><!--';
		foreach($this->m_aRequests as $oReq) {
			$sRet .= $oReq->getOutput(false);
		}
		$sRet .= '</script>';		
		return $sRet;
	}
}
?>