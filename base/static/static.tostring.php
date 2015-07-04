<?php
/**
 * Class to parse any given variable into a string representation.
 * 
 * @example echo ToString::parse($m);
 * @author Jelle Voet
 * @version 0.1.0
 */
class ToString {

	/**
	 * No special flags are defined.
	 */
	const NONE = 0;
	/**
	 * Use multiline in the output.
	 */
	const MULTILINE = 1;
	/**
	 * Use indentation in the output.
	 */
	const INDENTED = 2;
	/**
	 * Add quotes around string variables.
	 */
	const QUOTED = 4;
	/**
	 * Shorten strings that are to long.
	 */
	const CHOP_STRING = 8;
	/**
	 * Shorten arrays with to many elements.
	 */
	const CHOP_ARRAY = 16;
	
	private static $s_nFlags;
	private static $s_nRecursions;
	
	private $m_sKey = null;
	private $m_sName = 'Unknown type';
	private $m_aSubData = null;
	
	/**
	 * Create a new instance which auto parses the given data and populates $this->m_sName.
	 * The order of parsing is:
	 * - callbacks
	 * - strings
	 * - numbers
	 * - constants
	 * - resources
	 * - arrays
	 * - objects
	 * 
	 * @param mixed $mData
	 * @param string $sKey
	 */
	private function __construct($mData, $sKey = null) {
		$this->m_sKey = $sKey;
		
		if(self::$s_nRecursions++ < 10) {
			if(!$this->parseCallback($mData) &&
			   !$this->parseString($mData) &&
			   !$this->parseNumeric($mData) &&
			   !$this->parseConst($mData) &&
			   !$this->parseResource($mData) &&
			   !$this->parseArray($mData) &&
			   !$this->parseObject($mData)
			) {
				$this->m_sKey = null;
				$this->m_sName = 'Unknown type';
				$this->m_aSubData = null;
			}
		}
		else {
			$this->m_sKey = null;
			$this->m_sName = 'Recursion limit';
			$this->m_aSubData = null;
		}
		--self::$s_nRecursions;
	}
	
	/**
	 * Return true when the given data is a valid callback, and the $this->m_sName is set.
	 * Recognised formats are:
	 * - a 'callable' reference
	 * - existing global functions
	 * - an array with an object and a string
	 * - An array with a string to a valid class, and a string
	 *
	 * @param mixed $cbData
	 * @return boolean
	 */
	private function parseCallback($cbData) {
		if(is_callable($cbData, false, $this->m_sName)) {
			return true;
		}
		
		if(is_string($cbData) && function_exists($cbData)) {
			$this->m_sName = $cbData;
			return true;
		}
			
		if(is_array($cbData) && count($cbData) == 2 && isset($cbData[0]) && isset($cbData[1]) && is_string($cbData[1])) {
			if(is_object($cbData[0])) {
				$this->m_sName = get_class($cbData[0]) . '::' . $cbData[1];
				return true;
			}
			
			if(is_string($cbData[0]) && class_exists($cbData[0])) {
				$this->m_sName = $cbData[0] . '::' . $cbData[1];
				return true;
			}
		}

		return false;
	}
	
	/**
	 * Return true when the given data is a valid string, and the $this->m_sName is set.
	 * If flag contains CHOP_STRING, strings longer than 50 will be chopped.
	 * if flag contains QUOTED, strings will be encapsulated with quotes.
	 *
	 * @param mixed $sData
	 * @return boolean
	 */
	private function parseString($sData) {
		if(!is_string($sData))
			return false;

		if(self::$s_nFlags & self::CHOP_STRING && Encoding::length($sData) > 50) {
			$sData = Encoding::substring($sData, 0, 50) . '...';
		}

		if(self::$s_nFlags & self::QUOTED) {
			$sData = var_export($sData, true);
		}

		$this->m_sName = $sData;
		return true;
	}
	
	/**
	 * Return true when the given data is a valid number, and the $this->m_sName is set.
	 * $this->m_sName will be the numeric representation from strval().
	 *
	 * @see strval()
	 * @param mixed $nData
	 * @return boolean
	 */
	private function parseNumeric($nData) {
		if(!is_numeric($nData))
			return false;

		$this->m_sName = strval($nData);
		return true;
	}
	
	/**
	 * Return true when the given data is a valid constant, and the $this->m_sName is set.
	 * Valid constanta are true, false, null.
	 *
	 * @param mixed $mData
	 * @return boolean
	 */
	private function parseConst($mData) {
		if($mData === true) {
			$this->m_sName = 'True';
			return true;
		}
		if($mData === false) {
			$this->m_sName = 'False';
			return true;
		}
		if($mData === null) {
			$this->m_sName = 'Null';
			return true;
		}

		return false;
	}
	
	/**
	 * Return true when the given data is a valid resource, and the $this->m_sName is set.
	 * $this->m_sName will be the resource type from get_resource_type().
	 *
	 * @see get_resource_type()
	 * @param mixed $cbData
	 * @return boolean
	 */
	private function parseResource($hData) {
		if(!is_resource($hData))
			return false;

		$this->m_sName = get_resource_type($hData);
		return true;
	}

	/**
	 * Return true when the given data is a valid array, and the $this->m_sName is set.
	 * If flag contains CHOP_ARRAY, arrays longer than 25 items will be chopped.
	 * This method will instantiate child instances with their representing key value.
	 * These children will be added to $this->m_aSubData.
	 *
	 * @param mixed $cbData
	 * @return boolean
	 */
	private function parseArray($aData) {
		if(!is_array($aData))
			return false;

		if(self::$s_nFlags & self::CHOP_ARRAY && count($aData) > 25) {
			$aData = array_slice($aData, 0, 25);
			array_push($aData, '...');
		}

		$this->m_sName = 'Array';
		$this->m_aSubData = array();
		foreach($aData as $mKey => $mValue) {
			$oSubData = new ToString($mValue, strval($mKey));
			$this->m_aSubData []= $oSubData;
		}
		return true;
	}
	
	/**
	 * Return true when the given data is a valid object, and the $this->m_sName is set.
	 * This method will instantiate child instances with their representing variable-name value.
	 * These children will be added to $this->m_aSubData.
	 *
	 * @param mixed $cbData
	 * @return boolean
	 */
	private function parseObject($oData) {
		if(!is_object($oData))
			return false;

		$this->m_sName = 'Object['.get_class($o_sData).']';
		$this->m_aSubData = array();
		$aProperties = new ReflectionClass($oData);
		foreach($aProperties as $oProperty) {
			$oProperty->setAccesible(true);
			$oSubData = new ToString($oProperty->getValue($oData), $oProperty->getName());
			$this->m_aSubData []= $oSubData;
		}
		return true;
	}
	
	/**
	 * Parse the given variable into a string representation.
	 * Based on the given flags, you can define the output format.
	 * 
	 * This method is the public interface, but acts as a helper method which
	 * creates an internal ToString instance, and calls ToString::parseInstance()
	 * 
	 * @see ToString::__construct()
	 * @see ToString::parseInstance()
	 * @param mixed $mData
	 * @param int $nFlags
	 * @return string
	 */
	public static function parse($mData, $nFlags = self::NONE) {
		self::$s_nRecursions = 0;
		self::$s_nFlags = $nFlags;
		
		$oData = new ToString($mData);
		return self::parseInstance($oData);
	}
	
	/**
	 * Parse and format the given ToString instance.
	 * Before formatting, apply the given indentation if allowed.
	 * 
	 * @param ToString $oToString
	 * @param string $sIndentation
	 * @return string
	 */
	private static function parseInstance(ToString $oToString, $sIndentation = '') {
		$sReturn = '';
		if(self::$s_nFlags & self::INDENTED) $sReturn .= $sIndentation;
		if($oToString->m_sKey)  $sReturn .= $oToString->m_sKey . ': ';
		$sReturn .= $oToString->m_sName;
		if(is_array($oToString->m_aSubData)) {
			$sReturn .= '(';
			if(count($oToString->m_aSubData) > 0 && self::$s_nFlags & self::MULTILINE) $sReturn .= "\n";
			foreach($oToString->m_aSubData as $oSubData) {
				$sReturn .= self::parseInstance($oSubData, $sIndentation . "\t");
			}
			if(count($oToString->m_aSubData) > 0 && self::$s_nFlags & self::INDENTED) $sReturn .= $sIndentation;
			$sReturn .= ')';
		}
		if(self::$s_nFlags & self::MULTILINE) $sReturn .= "\n";
		return $sReturn;
	}
}
