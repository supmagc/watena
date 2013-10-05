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
	
	private $m_oCallbackContext;
	private $m_sCallback;
	private $m_aArguments = array();
	private $m_aValues = array();
	
	/**
	 * Create a new Server-class that will automatically try to process a TMX-request
	 *
	 * @param bool $bAutoExit automatically call exit after dataprocessing and your TMX_AjaxPage is flushed
	 * @param string $sPrefix
	 */
	public function __construct($oCallbackContext = null) {
		$this->m_oCallbackContext = $oCallbackContext;
		
		if(isset($_POST['callabck'])) {
			$this->m_sCallback = $_POST['callback'];
		}
		if(isset($_POST['args'])) {
			$this->m_aArguments = json_decode($_POST['args']);
		}
		if(isset($_POST['value'])) {
			$this->m_aValues = json_decode($_POST['values']);
		}
	}
	
	public function setCallbackContext($oCallbackContext) {
		$this->m_oCallbackContext = $oCallbackContext;
	}
	
	public function getCallbackContext() {
		return $this->m_oCallbackContext;
	}
	
	public function setCallback($sCallback) {
		$this->m_sCallback = $sCallback;
	}
	
	public function getCallback() {
		return $this->m_sCallback;
	}
	
	public function setArguments(array $aArguments) {
		$this->m_aArguments = $aArguments;
	}
	
	public function getArguments() {
		return $this->m_aArguments;
	}
	
	public function setValues(array $aValues) {
		$this->m_aValues = $aValues;
	}

	public function setValue($sName, $mValue) {
		$this->m_aValues[$sName] = $mValue;
	}
	
	public function getValues() {
		return $this->m_aValues;
	}
	
	public function getValue($sName) {
		return array_value($this->m_aValues, $sName, null);
	}

	/**
	 * Process and possibly exit thie TMX_Server request
	 * 
	 * @param bool $bAutoExit
	 */
	public function process() {
		if(!empty($this->m_sCallback)) {
			if(!empty($this->m_oCallbackContext)) {
				call_user_func_array(array($this->getCallbackContext(), $this->getCallback()), $this->getArguments());
			}
			else {
				call_user_func_array($this->getCallback(), $this->getArguments());
			}
		}
	}
}
?>