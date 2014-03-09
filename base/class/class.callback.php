<?php

/**
 * Generic callback object.
 * Reads and callback data from POST or GET data.
 * Formats variables as valid callback string for POST or GET.
 * 
 * @author Jelle Voet
 * @version
 */
class Callback extends Object {
	
	private $m_sMethod;
	private $m_aArguments;
	
	public function __construct($sMethod, array $aArguments = array()) {
		$this->m_sMethod = $sMethod;
		$this->m_aArguments = $aArguments;
	}
	
	public final function getMethod() {
		return $this->m_sMethod;
	}
	
	public final function getArguments() {
		return $this->m_aArguments;
	}
	
	public final function getArgument($nIndex, $mDefault = null) {
		return ($nIndex >= 0 && $nIndex < count($this->m_aArguments)) ? $this->m_aArguments[$nIndex] : $mDefault;
	}
	
	public final function getArgumentsLength() {
		return count($this->m_aArguments);
	}
	
	public final function process($oTarget = null) {
		if(empty($oTarget)) {
			if(!function_exists($this->m_sMethod)) {
				$this->getLogger()->warning('Callback requires the function {method} to exists in the current global scope.', array('method' => $this->getMethod()));
				return false;
			}
			
			return call_user_func_array($this->m_sMethod, $this->m_aArguments);
		}
		else {
			if(!is_object($oTarget)) {
				$this->getLogger()->warning('Callback requires the target variable to be an object.', array('method' => $this->getMethod(), 'target' => $this->getMethod()));
				return false;
			}
			if(!method_exists($oTarget, $this->m_sMethod)) {
				$this->getLogger()->warning('Callback requires the function {method} to exists in the target\'s object scope.', array('method' => $this->getMethod(), 'target' => $oTarget));
				return false;
			}
			
			return call_user_func_array(array($oTarget, $this->m_sMethod), $this->m_aArguments);
		}
	}
	
	public static function loadFromRequest() {
		$aData = array();
		$aArguments = array();
		if(isset($_GET['method'])) $aData = $_GET;
		if(isset($_POST['method'])) $aData = $_POST;
		
		if(!empty($aData)) {
			$sMethod = $aData['method'];
			
			if(isset($aData['arguments'])) {
				$aArguments = json_decode($aData['arguments'], true);
			}
			else if(isset($aData['args'])) {
				$aArguments = json_decode($aData['args'], true);
			}
			
			return new Callback($sMethod, $aArguments);
		}
		else {
			return null;
		}
	}
}

?>