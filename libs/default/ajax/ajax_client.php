<?php

/**
 * Client AJAX class that processes all registered requests
 *
 * @author Voet Jelle
 * @version 1.0.0 beta
 */
class AJAX_Client {
	
	private $m_aRequests = array();
	private $m_sJavascriptLink;
	
	/**
	 * Create a new client instance.
	 * If no javascriptlink is provided, you take responsability to load the ajax.js file.
	 * If indeed a links is provided, make sure it's absolute, no more processing will be done.
	 *
	 * @param string $sJavascriptLink
	 */
	public function __construct($sJavascriptLink = null) {
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
	 * Return the javascript-links passed to the constructor.
	 * 
	 * @return string
	 */
	public function getJavascriptLink() {
		return $this->m_sJavascriptLink;
	}
	
	/**
	 * Process all registered requests
	 *
	 * @param bool $bEcho choose if all data should be auto-echo-ed
	 * @return string
	 */
	public function getOutput() {
		$sRet = '';
		if(!empty($this->m_sJavascriptLink))
			$sRet .= <<<EOT
<script language="javascript 1.8" type="text/javascript"><!--
window['ajax'] = window['ajax'] || (function(doc, tag, src, ele, tar) {
	ele = doc.createElement(tag);
	tar = doc.getElementsByTagName(tag)[0];
	ele.async = 1;
	ele.src = src;
	tar.parentNode.insertBefore(ele, tar)
})(document, 'script', '{$this->getJavascriptLink()}');
--></script>
EOT;
		$sRet .= '<script language="javascript 1.8" type="text/javascript"><!--';
		foreach($this->m_aRequests as $oReq) {
			$sRet .= "\n" . $oReq->getOutput(false);
		}
		$sRet .= "\n</script>";		
		return $sRet;
	}
}
?>