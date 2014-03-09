<?php

/**
 * Class encapsulating a call to an onpage JavaScript Function.
 * With this class you can define and execute a JavaScript callback.
 * 
 * @author Jelle Voet
 * @version 1.0.0
 */
class JSFunction extends Object {
	
	private $m_sFunctionName;
	private $m_aParams;
	
	/**
	 * Create a new JavaScript Function instance.
	 * The $aParams parameter is optional but should only contains values.
	 * If you still provide an associative array, the keys will be stripped.
	 * If $sFunctionName is not valid a warning will be triggered.
	 * 
	 * @see JSFunction::isValidFunctionName()
	 * @param string $sFunctionName
	 * @param array $aParams
	 */
	public function __construct($sFunctionName, array $aParams = array()) {
		if(!self::isValidFunctionName($sFunctionName)) {
			$this->getLogger()->warning('The provided functionName \'{functionname}\' for the JSFunction instance is not valid and might produces JavaScript error\'s.', array('functionname' => $sFunctionName));
		}
		$this->m_sFunctionName = $sFunctionName;
		$this->m_aParams = array_values($aParams);
	}
	
	/**
	 * Get the JavaScript function name as provided during construction.
	 * 
	 * @return string
	 */
	public final function getFunctionName() {
		return $this->m_sFunctionName;
	}

	/**
	 * Get the values of the JavaScript function parameters array you provided during construction.
	 * 
	 * @return array
	 */
	public final function getParameters() {
		return $this->m_aParams;
	}

	/**
	 * Get a valid javascript function definition containing a call to the defined functionname.
	 * Format: function() {window['functionname'].apply(this, [parameters]);}
	 * 
	 * @return string
	 */
	public final function getFunction() {
		return sprintf('function() {window[\'%s\'].apply(this, %s);}', $this->m_sFunctionName, json_encode($this->m_aParams));
	}

	/**
	 * Get a valid callback variable declaration containing a call to the defined functionname.
	 * 
	 * @see JSFunction::getFunction()
	 * @param string $sCallbackName The callback variable name.
	 * @param string $bCloseStatement Do you cant to close the statement. (append ';')
	 * @return string
	 */
	public final function getCallback($sCallbackName, $bCloseStatement = true) {
		return sprintf('var %s = %s%s', $sCallbackName, $this->getFunction(),$bCloseStatement ? ';' : '');
	}
	

	/**
	 * Get a valid call for the function defined as functionname.
	 * 
	 * @see JSFunction::getFunction()
	 * @param string $bCloseStatement Do you cant to close the statement. (append ';')
	 * @return string
	 */
	public final function callFunction($bCloseStatement = true) {
		return sprintf('(%s)()%s', $this->getFunction(), $bCloseStatement ? ';' : '');
	}
	
	/**
	 * Returns the same as JSFunction::getFunction().
	 * This enabled the class to be used and concatenated as string variable.
	 * 
	 * @see JSFunction::getFunction()
	 * @see Object::toString()
	 * @return string
	 */
	public final function toString() {
		return $this->getFunction();
	}
	
	/**
	 * Check if the given functionname is valid.
	 * Valid format: /^[a-z_][a-z0-9_]*$/i
	 * 
	 * @param string $sFunctionName
	 * @return boolean
	 */
	public final static function isValidFunctionName($sFunctionName) {
		return Encoding::regMatch('^[a-z_][a-z0-9_]*$', $sFunctionName, 'i');
	}
}

?>