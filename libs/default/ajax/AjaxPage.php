<?php

/**
 * Class that outputs the TMX-response
 *
 * @author Voet Jelle - TomO-design
 * @version 1.0.0 beta
 */
class TMX_AjaxPage extends TMD_Page {
	
	private $m_oResponse = null;
	
	/**
	 * Create a new AjaxPage to output your TMX request
	 *
	 * @param TMX_Response $oResponse
	 */
	public function __construct(TMX_Response $oResponse) {
		$this->m_oResponse = $oResponse;
	}
	
	/**
	 * Set all page-headers
	 */
	protected function SetHeaders() {
		// not needed
	}
	
	/**
	 * Get the entire page/request content
	 */
	protected function GetContent() {
		return $this->m_oResponse->Process(false);
	}
}
?>