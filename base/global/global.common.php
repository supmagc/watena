<?php

/**
 * Stub function when 'not yet implemented'.
 * This will terminate the execution.
 */
function NYI() {
	terminate('NYI');
}

/**
 * Stub function when deprecated.
 * This will terminate the execution.
 */
function DEPR() {
	terminate('DEPRECATED');
}

/**
 * Terminate the execution and print the call-location and given message.
 * 
 * @param string $sMessage
 */
function terminate($sMessage) {
	$aTrace = debug_backtrace();
	if(count($aTrace) > 0) echo "<strong>{$aTrace[0]['file']}</strong> (<i>line: {$aTrace[0]['line']}</i>)\r\n";
	die($sMessage);
}

/**
 * Visualize the type and content of the given variable.
 * This will also print the location of the dump-call so you can easily remove it.
 * 
 * @param mixed $mVar
 */
function dump($mVar) {
	$aTrace = debug_backtrace();
	if(count($aTrace) > 0) echo "<strong>{$aTrace[0]['file']}</strong> (<i>line: {$aTrace[0]['line']}</i>)\r\n";
	echo '<pre>';
	var_dump($mVar);
	echo '</pre>';
}

/**
 * Unicode (UTF-8) variant of chr(...)
 * 
 * @see chr
 * @param mixed $mCodes
 * @return string
 */
function uchr($mCodes) {
    $sReturn = '';
    if(is_scalar($mCodes)) $mCodes = func_get_args();
    foreach($mCodes as $nCode) $sReturn .= html_entity_decode("&#$nCode;", ENT_NOQUOTES, 'UTF-8');
    return $sReturn;
}

/**
 * Convert the array version format to it's string representation.
 * 
 * @param array $aVersion
 * @return string
 */
function version2string(array $aVersion) {
	return sprintf('%d.%d.%d-%s', 
		array_value($aVersion, array('major'), 0), 
		array_value($aVersion, array('minor'), 0), 
		array_value($aVersion, array('build'), 0), 
		array_value($aVersion, array('state'), 'unknown')
	);
}

/**
 * Safe include function.
 * This function only exposes it's $sPath parameter to the included file.
 * It uses include_once and checks if the file exists and is readble before 
 * including, thus minimising the risk of an include warning.
 * 
 * !! Warning: don't use the error supression operator '@' since all errors 
 * triggered by including the file will be ignored as well.
 * 
 * @param string $sPath
 */
function include_safe($sPath) {
	$sPath = watena()->getPath($sPath, true);
	if(!is_readable($sPath))
		return false;
	include_once $sPath;
	return true;
}

/**
 * Get a timestamp when the file was last created or changed.
 * 
 * @param string $sPath
 * @return int
 */
function filechangetime($sPath) {
	$sPath = watena()->getPath($sPath, true);
	return max(filemtime($sPath), filectime($sPath));
}

/**
 * Retrieve a more cleaned version of the phpinfo(...) data.
 * This will remove the <html>, <head> and <body> tags.
 * Notice that this function will also echo it's content.
 * 
 * @param int $nWhat 
 */
function phpinfo_clean($nWhat = INFO_ALL) {
	ob_start();
	phpinfo($nWhat);
	$sData = ob_get_clean();
	$nIndexStart = Encoding::indexOf($sData, '<body>');
	$nIndexStop = Encoding::indexOf($sData, '</body>');
	$nIndexStart += Encoding::length($nIndexStart) + 2;
	$sData = Encoding::substring($sData, $nIndexStart, $nIndexStop - $nIndexStart);
	$sData = Encoding::regReplace('<a href="http://www.php.net/">(.*?)</a>', '', $sData);
	$sData = Encoding::replace(' width="600"', '', $sData);
	echo $sData;
}

/**
 * Assure the array structure exists as provided
 * The structure is as defined by the keys array
 * If an none existing key is found, it's created and set to the value
 * The default behaviour sets the ending index to the provided value
 * If you only want to assure this valkue is set, even with an existing value
 * you need to specify the $bOverwrite flag. (default: true)
 * 
 * @param array $arr
 * @param array $aKeys
 * @param mixed $mValue
 * @param bool $bOverwrite
 */
function array_assure(array &$arr, array $aKeys, $mValue = array(), $bOverwrite = true) {
	// Copy a reference to the array for resoration purposes
	// This means that at the end of the rouytine we store this reference
	// If this isn't done, the calling routine will have an invalid ref
	$aOld = &$arr;
	
	// Start looping through all the keys
	foreach($aKeys as $sKey) {
		
		// If the current value is no array, it means that the key cannot be found
		// In this case we force the variable to be an array
		if(!is_array($arr)) $arr = array();
		
		// If the array has no known index as specified by the given key
		// we set the current array value to the given default
		if(!isset($arr[$sKey])) $arr[$sKey] = $mValue;
		
		// We store a reference to the valid array index for the next iteration
		$arr = &$arr[$sKey];
	}
	
	// If we flagged this routine to overwrite optional value (default behaviour)
	// We overwrite the ending reference no mather what
	if($bOverwrite) $arr = $mValue;
	
	// Restore the original array
	$arr = &$aOld;
}

/**
 * Retrieve the values within the given array structure
 * The value lookup happens as defined by the keys array
 * If the key mapping cannot proceed (as readonly) the function
 * returns a default value. (default: null)
 * This value can be specified as a third optional parameter
 * 
 * @param array $arr
 * @param array $aKeys
 * @param mixed $mDefault
 */
function array_value(array &$arr, $aKeys, $mDefault = null) {
	// Make sure keys is an array, even when single value
	if(!is_array($aKeys)) $aKeys = array($aKeys);
	
	// Copy a reference to the array for internal usage
	// In contrast to array_assure, we use this reference as a helper
	// This optimisation saves us a couple lines
	$aHelper = &$arr;
	
	// Start looping through all keys
	foreach($aKeys as $sKey) {
		
		// Since we have a key value, we start by checking if we have a valid array
		// If this is not the case, we know wen can't check for the key 
		// Thus we return the default value
		if(!is_array($aHelper)) return $mDefault;
		
		// Since we have a key and an array, we need to make sure the match
		// We check if the array has an ellement that matches with the given key
		// If this is somehow not the case, we return the default value
		if(!isset($aHelper[$sKey])) return $mDefault;
		
		// In all other cases, we just proceed with the array reference
		$aHelper = &$aHelper[$sKey];
	}
	
	// We arrived ath the end of our routine without an early return
	// This means all has gone well, and we can simply return the value encompased with $aHelper
	return $aHelper;
}

/**
 * Traverse the given array and check if the provided callback yields true for all values
 * 
 * @param callback $mCallback
 * @param array $aData
 * @return boolean
 */
function array_all($mCallback, array &$aData) {
	$bResult = true;
	foreach($aData as $mKey => $mValue) $bResult = $bResult && call_user_func($mCallback, $mValue);
	return $bResult;
}

/**
 * Retrieve the last element of an array.
 * 
 * @param array $arr The array from which you need the last value.
 * @param mixed $mDefault Default return value when array size is 0.
 * @return mixed|$mDefault The last value, or $mDefault when empty array.
 */
function array_first(array &$arr, $mDefault = null) {
	if(count($arr) == 0) return $mDefault;
	$aKeys = array_keys($arr);
	return $arr[$aKeys[0]];
}

/**
 * Retrieve the last element of an array.
 * 
 * @param array $arr The array from which you need the last value.
 * @param mixed $mDefault Default return value when array size is 0.
 * @return mixed|$mDefault The last value, or $mDefault when empty array.
 */
function array_last(array &$arr, $mDefault = null) {
	if(count($arr) == 0) return $mDefault;
	$aKeys = array_keys($arr);
	return $arr[$aKeys[count($aKeys) - 1]];
}

/**
 * Try to make sure the file at the given path exists and has the right permissions.
 * If some of these conditions are not met, they will me fixed if possible,
 * or the function will return false
 * 
 * @param string $sPath
 * @param number $nMode (default: 0664)
 * @return boolean If the file could be assured or not.
 */
function file_assure($sPath, $nMode = 0664) {
	$sDirectory = dirname($sPath);
	if(!file_exists($sDirectory) && !mkdir($sDirectory, 0775, true)) return false;
	if(!file_exists($sPath) && file_put_contents($sPath, '') === false) return false;
	if(substr(sprintf('%o', fileperms($sPath)), - 3) != sprintf('%o', $nMode) && !chmod($sPath, $nMode)) return false;
	return true;
}

function dir_assure($sPath, $nMode = 0775) {
	if(!file_exists($sPath) && !mkdir($sPath, $nMode, true)) return false;
	if(substr(sprintf('%o', fileperms($sPath)), - 3) != sprintf('%o', $nMode) && !chmod($sPath, $nMode)) return false;
	return true;
}

/**
 * Check if the given array is associatrive
 * 
 * @param array $arr The array to check for associativeness.
 * @return boolean
 */
function is_assoc(&$arr) {
	// Make sure it's an array'
	if(!is_array($arr)) return false;
	
	// This is in fact a for-each loop where all keys are checkt to match an incremental counter.
	// If the loop breaks, the key/counter didn't match and the key will be valid.
	for(reset($arr), $i=0 ; key($arr) === $i ; next($arr), ++$i);
	return !is_null(key($arr));
}

function is_alphabetical($var) {
	return ctype_alpha($var);
}

function is_alphanumeric($var) {
	return ctype_alnum($var);
}

function is_whitespace($var) {
	return ctype_space($var);
}

function is_email($var) {
	return filter_var($var, FILTER_VALIDATE_EMAIL);
}

function fullurlencode($sData) {
	$sReturn = '';
	for ($i=0 ; $i<strlen($sData) ; ++$i) {
		$sReturn .= '%' . bin2hex($sData[$i]);
	}
	return $sReturn;	
}

function fullurldecode($sData) {
	return pack("H*" , str_replace('%', '', $sData));
}

function explode_trim($sSplitter, $sData) {
	$aData = explode($sSplitter, $sData);
	$aReturn = array();
	foreach($aData as $sKey => $sValue) {
		$sValue = trim($sValue);
		if(strlen($sValue) > 0)
			$aReturn[$sKey] = $sValue;
	}
	return array_merge($aReturn);
}

function implode_trim($sGlue, array $aData) {
	$sReturn = '';
	foreach($aData as $sValue) {
		$sValue = trim($sValue);
		if(strlen($sValue) > 0) {
			if(strlen($sReturn) > 0) $sReturn .= $sGlue;
			$sReturn .= $sValue;
		}
	}
	return $sReturn;
}

function implode_assoc($sGlueEle, $sGlueAssoc, array $aData) {
	$sReturn = '';
	foreach($aData as $mKey => $mValue) {
		if(strlen($sReturn) > 0) $sReturn .= $sGlueEle;
		$sReturn .= $mKey . $sGlueAssoc . $mValue;
	}
	return $sReturn;
}

/**
 * Add Backslashes to string you want to use in ereg-expressions
 *
 * @param string $sString
 * @return string
 */
function addEregSlashes($sString) {
	return addcslashes($sString, '[\\^$.|?*+(){}');
}

/**
 * Add Backslashes to string you want to use in preg-expressions
 *
 * @param string $sString
 * @return string
 */
function addPregSlashes($sString) {
	return preg_quote($sString, '/');
}

function toStringSingleLine($mData) {
	return ToString::parse($mData, ToString::CHOP_ARRAY | ToString::CHOP_STRING);
}

function toStringIntended($mData) {
	return ToString::parse($mData, ToString::CHOP_ARRAY | ToString::CHOP_STRING | ToString::MULTILINE | ToString::INDENTED);
}

/**
 * Parse all external links included in a the given content var
 * WARNING: the first param ($sContent) is used as a call by reference for optimalisation
 * WARNING: only set $bOnlyAbsolute to false in case you need it (since it runs an extra preg_match and runs 2 extra str_replace
 * WARNING: don't try to inject your own regex since they are escaped
 *
 * @param string $sContent
 * @param mixed $mTag
 * @param mixed $mAttribute
 * @param mixed $mExtension
 * @param string $sRemove
 * @param string $sAppend
 * @param bool $bRemoveOptional
 * @param bool $bStripBack
 * @param bool $bOnlyAbsolute
 */
function ParseExternalLinks(&$sContent, $mTag, $mAttribute, $mExtension, $sRemove,  $sAppend, $bRemoveOptional = true, $bStripBack = true, $bOnlyAbsolute = true) {
	// Check if the params are correct
	if(!is_string($sContent) || !is_string($sRemove) || !is_string($sAppend)) {
		trigger_error('Illegal params for data-manipulation', 'Ensure you are using the correct type of var as param', E_USER_WARNING);
	}
	
	// some optimalisations
	if(!$bRemoveOptional && Encoding::length($sRemove) > 0 && Encoding::substring($sRemove, 0, 1) == '/') {
		$bOnlyAbsolute = true;
		$sRemove = Encoding::substring($sRemove, 1);
	}
	else if($bOnlyAbsolute && Encoding::length($sRemove) > 0 && Encoding::substring($sRemove, 0, 1) == '/') {
		$sRemove = Encoding::substring($sRemove, 1);
	}
	
	// Create the correct vars for the preg-expressions
	$sTag = is_array($mTag) ? implode('|', array_map(array('TMD_Data', 'AddPregSlashes'), $mTag)) : TMD_Data::AddPregSlashes($mTag);
	$sAttribute = is_array($mAttribute) ? implode('|', array_map(array('TMD_Data', 'AddPregSlashes'), $mAttribute)) : TMD_Data::AddPregSlashes($mAttribute);
	$sExtension = is_array($mExtension) ? implode('|', array_map(array('TMD_Data', 'AddPregSlashes'), $mExtension)) : TMD_Data::AddPregSlashes($mExtension);
	$sRemove = TMD_Data::AddPregSlashes(preg_replace('/^\//', '', $sRemove));
	$sExtension = !empty($sExtension) ? '()' : "\.($sExtension)";
	
	// Save the external links if needed
	$aExternalLinks = array();
	if(!$bOnlyAbsolute && $bRemoveOptional && Encoding::length($sRemove) > 0) {
		$aMatches = array();
		$aPositions = array();
		Encoding::regFindAll("/<($sTag) .*?($sAttribute)=[\"'](http|https|mailto|callto):\/\/.*?[\"'].*?>/i", $sContent, $aMatches, $aMatches);
		foreach($aMatches as $sMatch) {
			$aExternalLinks[md5($sMatch[0])] = $sMatch[0];
		}
		$sContent = Encoding::replace(array_values($aExternalLinks), array_keys($aExternalLinks), $sContent);
	}
	
	// Parse absolute URL's
	if($bOnlyAbsolute) {
		if($bStripBack) $sContent = Encoding::regReplace("/(<($sTag) .*?($sAttribute)=[\"'])\/(\.\.\/)*(.+?".$sExtension."[\"'].*?>)/i", "\\1/\\5", $sContent);
		$sContent = Encoding::regReplace("/(<($sTag) .*?($sAttribute)=[\"'])\/($sRemove)".($bRemoveOptional ? '?' : '')."(.+?".$sExtension."[\"'].*?>)/i", "\\1$sAppend\\5", $sContent);
	}
	
	// Parse none-absolute URL's
	else {
		if($bStripBack) $sContent = Encoding::regReplace("/(<($sTag) .*?($sAttribute)=[\"'])(\.\.\/)*(.+?".$sExtension."[\"'].*?>)/i", "\\1\\5", $sContent);
		$sContent = Encoding::regReplace("/(<($sTag) .*?($sAttribute)=[\"'])($sRemove)".($bRemoveOptional ? '?' : '')."(.+?".$sExtension."[\"'].*?>)/i", "\\1$sAppend\\5", $sContent);
	}

	// Restore the external links if needed
	if(!$bOnlyAbsolute && $bRemoveOptional && Encoding::length($sRemove) > 0) {
		$sContent = Encoding::replace(array_keys($aExternalLinks), array_values($aExternalLinks), $sContent);
	}
	
	// since we are using call by reference, we don't need to return the $sContent
}

/********************************
 * Retro-support of get_called_class()
 * Tested and works in PHP 5.2.4
 * http://www.sol1.com.au/
 ********************************/
if(!function_exists('get_called_class')) {
function get_called_class($bt = false,$l = 1) {
    if (!$bt) $bt = debug_backtrace();
    if (!isset($bt[$l])) throw new Exception("Cannot find called class -> stack level too deep.");
    if (!isset($bt[$l]['type'])) {
        throw new Exception ('type not set');
    }
    else switch ($bt[$l]['type']) {
        case '::':
            $lines = file($bt[$l]['file']);
            $i = 0;
            $callerLine = '';
            do {
                $i++;
                $callerLine = $lines[$bt[$l]['line']-$i] . $callerLine;
            } while (stripos($callerLine,$bt[$l]['function']) === false);
            preg_match('/([a-zA-Z0-9\_]+)::'.$bt[$l]['function'].'/',
                        $callerLine,
                        $matches);
            if (!isset($matches[1])) {
                // must be an edge case.
                throw new Exception ("Could not find caller class: originating method call is obscured.");
            }
            switch ($matches[1]) {
                case 'self':
                case 'parent':
                    return get_called_class($bt,$l+1);
                default:
                    return $matches[1];
            }
            // won't get here.
        case '->': switch ($bt[$l]['function']) {
                case '__get':
                    // edge case -> get class of calling object
                    if (!is_object($bt[$l]['object'])) throw new Exception ("Edge case fail. __get called on non object.");
                    return get_class($bt[$l]['object']);
                default: return $bt[$l]['class'];
            }

        default: throw new Exception ("Unknown backtrace method type");
    }
}
}
