<?php

/**
 * Class processing a TMX-request server-sided
 *
 * @author Voet Jelle - ToMo-design
 * @version 2.1.0 beta
 * 
 * VERSION-LOG
 * -----------
 * 
 * 30-8-2010: 2.0.0 => 2.1.0
 * - Added JSON support when sending data to the server (we now support arrays and advanced types)
 * 
 * 30-7-2010: 1.0.0 => 2.0.0
 * - Anticipated all the changes within TMX_Request
 */
class AJAX_Server {
	
	private $m_sPrefix = 'TMX';
	private $m_oPage = null;
	
	/**
	 * Create a new Server-class that will automatically try to process a TMX-request
	 *
	 * @param bool $bAutoExit automatically call exit after dataprocessing and your TMX_AjaxPage is flushed
	 * @param string $sPrefix
	 */
	public function __construct($bAutoProcessAndExit = true, $sPrefix = 'TMX') {
		$this->m_sPrefix = $sPrefix;
		$oResponse = null;
		
		if($this->ValidRequestExists()) {
			try {
				$oResponse = $this->GetResponse();
				if(!($oResponse instanceof TMX_Response)) trigger_error('No TMX_response object returned');
			}
			catch(Exception $e) {
				$oResponse = TMX_Response::CreateErrorResponse(2, 'file: ' . $e->__toString() . $e->getFile() . ' [' . $e->getLine() . ']   ' .$e->getMessage());
			}
		}
		else {
			$oResponse = TMX_Response::CreateErrorResponse(3, 'No valid TMX-data was found in the POST-value-stream.');
		}
		$this->m_oPage = new TMX_AjaxPage($oResponse);
		
		if($bAutoProcessAndExit) {
			$this->Process(true);
		}
	}
	
	/**
	 * Retrieve the ourput-page
	 * 
	 * @return TMX_AjaxPage
	 */
	public function GetPage() {
		return $this->m_oPage;
	}

	/**
	 * Process and possibly exit thie TMX_Server request
	 * 
	 * @param bool $bAutoExit
	 */
	public function Process($bAutoExit = true) {
		$this->m_oPage->Process();
		if($bAutoExit) exit;
	}
	
	/**
	 * Check if valid request-data exist
	 *
	 * @return bool
	 */
	private function ValidRequestExists() {
		return 
			isset($_POST[$this->m_sPrefix.'_PHPCallback']) &&
			isset($_POST[$this->m_sPrefix.'_Args']) &&
			isset($_POST[$this->m_sPrefix.'_Values']);
	}
	
	/**
	 * Retrieve a valid response from the PHPCallback function based on the request-POST-data
	 *
	 * @return TMX_Response
	 */
	private function GetResponse() {
		$aCall = unserialize($_POST[$this->m_sPrefix.'_PHPCallback']);
		$aArgs = json_decode($_POST[$this->m_sPrefix.'_Args'], true);		
		$aValues = json_decode($_POST[$this->m_sPrefix.'_Values'], true);
		$aArgs []= $aValues;
		
		return call_user_func_array($aCall, $aArgs);
	}
}
?>