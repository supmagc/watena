<?php

abstract class Cacheable extends Object {

	/**
	 * This method can be used to invalidate an object when loaded from cache.
	 * This method is NOT called when newly creating an object.
	 */
	public function validate() {return true;}
	
	/**
	 * This method is called when initting the object and should leave the object in a cacheable/serializeable state.
	 */
	public function make(array $aMembers) {}
	
	/**
	 * This method is called when waking the object when loading it back from the cache.
	 * For example: creating a database connection should be done at this time.
	 */
	public function init() {}
	
	private $m_oData;
	
	public function __construct(CacheData $oData) {
		$this->m_oData = $oData;
	}
	
	public function getCacheData() {
		return $this->m_oData;
	}
	
	public function getConfiguration() {
		return $this->getCacheData()->getConfiguration();
	}
	
	public function getConfig($sKey, $mDefault = null) {
		return $this->getCacheData()->getConfig($sKey, $mDefault);
	}
	
	public function update() {
		return $this->getCacheData()->update($this);
	}
}

?>