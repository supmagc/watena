<?php

class CachePEAR extends Plugin implements ICache {

	private $m_oCache;
	private $m_sContainer;
	private $m_aOptions = array();
	
	public function make() {
		$this->m_sContainer = parent::getConfig('CONTAINER', 'file');
		$this->m_aOptions = parent::getConfig('OPTIONS', array());
		
		if(isset($this->m_aOptions['cache_dir'])) $this->m_aOptions['cache_dir'] = parent::getWatena()->getPath($this->m_aOptions['cache_dir']);
	}
	
	public function init() {
		$this->m_oCache = new Cache($this->m_sContainer, $this->m_aOptions);
	}
	
	public function retrieve($sKey, $cbRetriever, $nExpirationSec = 0, array $aParams = array(), $bForceRefresh = false) {
		$nID = $this->m_oCache->generateID($sKey);
		$mData = $bForceRefresh ? false : $this->m_oCache->load($nID);
		if(!$mData || $bForceRefresh) {
			$mData = call_user_func_array($cbRetriever, $aParams);
			$this->m_oCache->save($nID, $mData, $nExpirationSec);
			return $mData;
		}
		return $mData;
	}

	public function delete($sKey) {
		$this->m_oCache->remove(self::_getCache()->generateID($sKey));
	}

	public function flush() {
		$this->m_oCache->flush();
	}

	public function get($sKey, $mDefault) {
		$mData = $this->m_oCache->load($nID);
		if(!$mData) $mData = $mDefault;
		return $mData;
	}

	public function set($sKey, $mData) {
		return $this->m_oCache->save($nID, $mData, 0);
	}
	
	public function getVersion() {
		return array('major' => 0, 'minor' => 1, 'build' => 1, 'state' => 'dev');
	}

	public static function getRequirements() {
		return array('pear' => 'Cache');
	}
}

?>