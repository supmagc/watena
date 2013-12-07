<?php

interface ICache {
	
	public function retrieve($sKey, $cbRetriever, $nExpirationSec = 0, array $aParams = array(), $bForceRefresh = false);
	public function delete($sKey);
	public function flush();
	public function get($sKey, $mDefault);
	public function set($sKey, $mData);
}

?>