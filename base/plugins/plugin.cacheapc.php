<?php

class CacheAPC extends Plugin implements ICache {

	public function retrieve($sKey, $cbRetriever, $nExpirationSec = 0, array $aParams = array(), $bForceRefresh = false) {
		$bSucces = false;
		$mData = $bForceRefresh ? null : apc_fetch($sKey, $bSucces);
		if(!$bSucces || $bForceRefresh) {
			$mData = call_user_func_array($cbRetriever, $aParams);
			apc_store($sKey, $mData, $nExpirationSec);
		}
		return $mData;
	}

	public function delete($sKey) {
		apc_delete($sKey);
	}

	public function flush() {
		apc_clear_cache();
	}

	public function get($sKey, $mDefault) {
		$mData = apc_fetch($sKey, $bSucces);
		if(!$bSucces) $mData = $mDefault;
		return $mData;
	}
	
	public function set($sKey, $mData) {
		return apc_store($sKey, $mData, 0);
	}
	
	public function getVersion() {
		return array('major' => 0, 'minor' => 1, 'build' => 1, 'state' => 'dev');
	}

	public function getRequirements() {
		return array('extensions' => 'apc');
	}
}

?>