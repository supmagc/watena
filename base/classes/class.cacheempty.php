<?php

class CacheEmpty extends Object implements ICache {
	
	public function retrieve($sKey, $cbRetriever, $nExpirationSec, array $aParams = array()) {
		return call_user_func_array($cbRetriever, $aParams);
	}

	public function delete($sKey) {
		
	}
	
	public function flush() {
		
	}
}

?>