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
	
	private $m_sCallback;
	private $m_aArguments = array();
	private $m_aValues = array();
	
	/**
	 * Create a new Server-class that will automatically try to process a TMX-request
	 *
	 * @param bool $bAutoExit automatically call exit after dataprocessing and your TMX_AjaxPage is flushed
	 * @param string $sPrefix
	 */
	public function __construct() {
		if(isset($_POST['callback'])) {
			$this->m_sCallback = $_POST['callback'];
		}
		else if(isset($_GET['callback'])) {
			$this->m_sCallback = $_GET['callback'];
		}
		
		if(isset($_POST['args'])) {
			$this->m_aArguments = json_decode($_POST['args'], true);
		}
		else if(isset($_GET['args'])) {
			$this->m_aArguments = json_decode($_GET['args'], true);
		}
		
		if(isset($_POST['values'])) {
			$this->m_aValues = json_decode($_POST['values'], true);
		}
		else if(isset($_GET['values'])) {
			$this->m_aValues = json_decode($_GET['values'], true);
		}
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
	public function process($oContext = null) {
		if(!empty($this->m_sCallback)) {
			if(!empty($oContext)) {
				if(method_exists($oContext, $this->getCallback())) {
					return call_user_func_array(array($oContext, $this->getCallback()), $this->getArguments());
				}
			}
			else {
				return call_user_func_array($this->getCallback(), $this->getArguments());
			}
		}
		return false;
	}
}
?>