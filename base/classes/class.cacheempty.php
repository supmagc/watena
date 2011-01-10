<?php

class CacheEmpty extends Object implements ICache {
	
	public function retrieve($sKey, $cbRetriever, $nExpirationSec= 0, array $aParams = array(), $bForceRefresh = false) {
		return call_user_func_array($cbRetriever, $aParams);
	}

	public function delete($sKey) {
		
	}
	
	public function flush() {
		
	}

	public function get($sKey, $mDefault) {
		return $mDefault;
	}
	
	public function set($sKey, $mData) {
		return true;
	}
}

?>