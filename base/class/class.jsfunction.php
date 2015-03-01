<?php

/**
 * Class encapsulating a call to an onpage JavaScript Function.
 * With this class you can define and execute a JavaScript callback.
 * 
 * @author Jelle Voet
 * @version 1.0.0
 */
class JSFunction extends Object {
	
	private $m_sFunction;
	private $m_aParameters;
	
	/**
	 * Create a new JavaScript Function instance.
	 * The $aParams parameter is optional but should only contains values.
	 * If you still provide an associative array, the keys will be stripped.
	 * If $sFunctionName is not valid a warning will be triggered.
	 * 
	 * @see JSFunction::isValidFunction()
	 * @param string $sFunction
	 * @param array $aArguments
	 */
	public function __construct($sFunction, array $aParameters = array()) {
		if(!self::isValidFunction($sFunction)) {
			$this->getLogger()->warning('The provided function \'{function}\' for the JSFunction instance is not valid and might produces JavaScript error\'s.', array('function' => $sFunction));
		}
		$this->m_sFunction = $sFunction;
		$this->m_aParameters = array_values($aParameters);
	}
	
	/**
	 * Get the JavaScript function name as provided during construction.
	 * 
	 * @return string
	 */
	public final function getFunction() {
		return $this->m_sFunction;
	}

	/**
	 * Get the values of the JavaScript function parameters array you provided during construction.
	 * 
	 * @return array
	 */
	public final function getParameters() {
		return $this->m_aParameters;
	}
	
	/**
	 * Get the value of a specific javascript function parameter by index, of the specified efault value.
	 * 
	 * @param int $nIndex
	 * @param mixed $mDefault
	 * @return mixed
	 */
	public final function getParameter($nIndex, $mDefault = null) {
		return array_value($this->m_aParameters, $nIndex, $mDefault);
	}
	
	/**
	 * Get the length of the parameters array.
	 * 
	 * @return int
	 */
	public final function getParameterLength() {
		return count($this->m_aParameters);
	}

	/**
	 * Get a valid javascript function definition containing a call to the defined functionname.
	 * Format: function() {window['functionname'].apply(this, [parameters]);}
	 * 
	 * @return string
	 */
	public final function getAsDelegate() {
		return sprintf('function() {window[\'%s\'].apply(this, %s);}', $this->m_sFunction, json_encode($this->m_aParameters));
	}

	/**
	 * Get a valid callback variable declaration containing a call to the defined functionname.
	 * 
	 * @see JSFunction::getAsDelegate()
	 * @param string $sCallbackName The callback variable name.
	 * @param string $bCloseStatement Do you cant to close the statement. (append ';')
	 * @return string
	 */
	public final function getAsVariable($sCallbackName) {
		return sprintf('var %s = %s;', $sCallbackName, $this->getAsDelegate());
	}
	

	/**
	 * Get a valid call for the function defined as functionname.
	 * 
	 * @see JSFunction::getAsDelegate()
	 * @param string $bCloseStatement Do you cant to close the statement. (append ';')
	 * @return string
	 */
	public final function getAsCall() {
		return sprintf('(%s)();', $this->getAsDelegate());
	}
	
	/**
	 * Returns the same as JSFunction::getFunction().
	 * This enabled the class to be used and concatenated as string variable.
	 * 
	 * @see JSFunction::getAsDelegate()
	 * @see Object::toString()
	 * @return string
	 */
	public final function toString() {
		return $this->getAsDelegate();
	}
	
	/**
	 * Check if the given function is valid.
	 * Valid format: /^[a-z_][a-z0-9_]*$/i
	 * 
	 * @param string $sFunction
	 * @return boolean
	 */
	public final static function isValidFunction($sFunction) {
		return Encoding::regMatch('^[a-z_][a-z0-9_]*$', $sFunction, 'i');
	}
}
