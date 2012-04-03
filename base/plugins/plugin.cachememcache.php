<?php
require_extension('Memcache');

class CacheMemcache extends Plugin implements ICache {

	private $m_oCache;
	private $m_sHost;
	private $m_nPort;
	
	public function make() {
		$this->m_sHost = parent::getConfig('HOST', 'localhost');
		$this->m_nPort = parent::getConfig('PORT', 11211);
	}
	
	public function init() {
		$this->m_oCache = new Memcache();
		$this->m_oCache->addServer($this->m_sHost, $this->m_nPort);
	}
	
	public function retrieve($sKey, $cbRetriever, $nExpirationSec = 0, array $aParams = array(), $bForceRefresh = false) {
		$mData = $bForceRefresh ? false : $this->m_oCache->get($sKey);
		if(!$mData || $bForceRefresh) {
			$mData = call_user_func_array($cbRetriever, $aParams);
			$this->m_oCache->set($sKey, $mData, $nExpirationSec);
			return $mData;
		}
		return $mData;
	}

	public function delete($sKey) {
		$this->m_oCache->delete($sKey);
	}

	public function flush() {
		$this->m_oCache->flush();
	}

	public function get($sKey, $mDefault) {
		$aData = $this->m_oCache->get(array($sKey));
		return isset($aData[$sKey]) ? $aData[$sKey] : $mDefault;
	}

	public function set($sKey, $mData) {
		return $this->m_oCache->set($sKey, $mData, 0);
	}
	
	public function getVersion() {
		return array('major' => 0, 'minor' => 1, 'build' => 1, 'state' => 'dev');
	}
}

?>