<?php

class CachePEAR extends Plugin implements ICache {

	private $m_oCache;
	private $m_sContainer;
	private $m_aOptions = array();
	
	public function init() {
		$this->m_sContainer = parent::getConfig('CONTAINER', 'file');
		$this->m_aOptions = parent::getConfig('OPTIONS', array());
		
		if(isset($this->m_aOptions['cache_dir'])) $this->m_aOptions['cache_dir'] = parent::getWatena()->getPath($this->m_aOptions['cache_dir']);
	}
	
	public function retrieve($sKey, $cbRetriever, $nExpirationSec, array $aParams = array()) {
		$nID = self::_getCache()->generateID($sKey);
		$mData = self::_getCache()->load($nID);
		if(!$mData) {
			$mData = call_user_func_array($cbRetriever, $aParams);
			self::_getCache()->save($nID, $mData, $nExpirationSec);
			return $mData;
		}
		return $mData;
	}

	public function delete($sKey) {
		self::_getCache()->remove(self::_getCache()->generateID($sKey));
	}

	public function flush() {
		self::_getCache()->flush();
	}
	
	public function getVersion() {
		return array('major' => 0, 'minor' => 1, 'build' => 1, 'state' => 'dev');
	}

	public function getRequirements() {
		return array('pear' => 'Cache');
	}
	
	private function _getCache() {
		if(!$this->m_oCache) {
			$this->m_oCache = new Cache($this->m_sContainer, $this->m_aOptions);
		}
		return $this->m_oCache;
	}
}

?>