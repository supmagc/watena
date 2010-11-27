<?php

interface ICache {
	
	public function retrieve($sKey, $cbRetriever, $nExpirationSec, array $aParams = array());
	public function delete($sKey);
	public function flush();
}

?>