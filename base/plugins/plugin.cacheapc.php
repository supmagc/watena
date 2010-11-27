<?php

class CacheAPC extends Plugin implements ICache {

	public function retrieve($sKey, $cbRetriever, $nExpirationSec, array $aParams = array()) {
		$bSucces = false;
		$mData = apc_fetch($sKey, $bSucces);
		if(!$bSucces) {
			$mData = call_user_func_array($cbRetriever, $aParams);
			apc_store($sKey, $mData, $nExpirationSec);
		}
		return $mData;
	}

	public function delete($sKey) {
		apc_delete($sKey);
	}

	public function flush() {
		apc_flush();
	}
	
	public function init() {
	}
	
	public function getVersion() {
		return array('major' => 0, 'minor' => 1, 'build' => 1, 'state' => 'dev');
	}

	public function getRequirements() {
		return array('extensions' => 'apc');
	}
}

?>