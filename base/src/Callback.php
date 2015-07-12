<?php namespace Watena\Core;

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
	private $m_aParameters;
	
	/**
	 * Create an instance for the callback.
	 * If you want to load callback data from a source, use the static helper-method: Callback::LoadFromRequest()
	 * 
	 * @param string $sMethod Name of the method/function to call.
	 * @param array $aParameters An optional array with parameters to pass on $sMethod when this callback is processed.
	 */
	public function __construct($sMethod, array $aParameters = array()) {
		if(!self::isValidMethod($sMethod)) {
			$this->getLogger()->warning('The provided method \'{method}\' for the Callback instance is not valid and might produces error\'s.', array('method' => $sMethod));
		}
		$this->m_sMethod = $sMethod;
		$this->m_aParameters = array_values($aParameters);
	}
	
	/**
	 * Retrieve the method-/function-name this callback will call when processed.
	 * 
	 * @return string
	 */
	public final function getMethod() {
		return $this->m_sMethod;
	}

	/**
	 * Retrieve the optional list of arguments that will be used when calling the method/function.
	 * 
	 * @see Callback::getParametersLength()
	 * @return array
	 */
	public final function getParameters() {
		return $this->m_aParameters;
	}
	
	/**
	 * Rerieve the argument by index (or the default value).
	 * 
	 * @see Callback::getParametersLength()
	 * @param int $nIndex
	 * @param mixed $mDefault
	 * @return mixed
	 */
	public final function getParameter($nIndex, $mDefault = null) {
		return array_value($this->m_aParameters, $nIndex, $mDefault);
	}
	
	/**
	 * The length of the internal arguments array
	 * 
	 * @see Callback::getParameters()
	 * @see Callback::getParameter()
	 * @return int
	 */
	public final function getParametersLength() {
		return count($this->m_aParameters);
	}
	
	/**
	 * Process this callback.
	 * if a valid target is given, try to call the method on the target.
	 * If none given, try to call the global function.
	 * Returns the value of the callback method/function, or false.
	 * 
	 * @param Object $oTarget
	 * @return boolean|mixed
	 */
	public final function process($oTarget = null) {
		if(empty($oTarget)) {
			if(!function_exists($this->m_sMethod)) {
				$this->getLogger()->warning('Callback requires the function {method} to exists in the current global scope.', array('method' => $this->getMethod()));
				return false;
			}
			
			return call_user_func_array($this->m_sMethod, $this->m_aParameters);
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
			
			return call_user_func_array(array($oTarget, $this->m_sMethod), $this->m_aParameters);
		}
	}

	/**
	 * Check if the given method-/function-name is valid.
	 * Valid format: /^[a-z_][a-z0-9_]*$/i
	 *
	 * @param string $sMethod
	 * @return boolean
	 */
	public final static function isValidMethod($sMethod) {
		return Encoding::regMatch('^[a-z_][a-z0-9_]*$', $sMethod, 'i');
	}
	
	/**
	 * Try to load a valid Callback instance from request-data.
	 * 
	 * Supported arguments for method-/function-name:
	 * - method: as method/function name
	 * 
	 * Supported arguments for parameters-array:
	 * - arguments: as parameters (json-encoded)
	 * - args: as parameters (json-encoded)
	 * 
	 * @return Callback|null
	 */
	public static function loadFromRequest() {
		$aData = array();
		$aParameters = array();
		if(isset($_GET['method'])) $aData = $_GET;
		if(isset($_POST['method'])) $aData = $_POST;
		
		if(!empty($aData)) {
			$sMethod = $aData['method'];
			
			if(isset($aData['arguments'])) {
				$aParameters = json_decode($aData['arguments'], true);
			}
			else if(isset($aData['args'])) {
				$aParameters = json_decode($aData['args'], true);
			}
			
			return new Callback($sMethod, $aParameters);
		}
		else {
			return null;
		}
	}
}
