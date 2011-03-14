<?php

function terminate($sMessage) {
	die($sMessage);
}

/**
 * Assure the array structure exists as provided
 * The structure is as defined by the keys array
 * If an none existing key is found, it's created and set to the value
 * The default behaviour sets the ending index to the provided value
 * If you only want this to happen when the index is none-existing
 * you need to specify the $bOverwrite flag as true
 * 
 * @param array $arr
 * @param array $aKeys
 * @param mixed $mValue
 * @param bool $bOverwrite
 */
function array_assure(&$arr, array $aKeys, $mValue = array(), $bOverwrite = true) {
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
 * returns a default value.
 * This value can be specified as a third optional parameter
 * 
 * @param array $arr
 * @param array $aKeys
 * @param mixed $mDefault
 */
function array_value(&$arr, array $aKeys, $mDefault = null) {
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

?>