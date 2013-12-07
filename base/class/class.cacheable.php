<?php

/**
 * Base class for caching behaviour.
 * The caching protocol works as follows:
 * 1) The user calls the create on one of the child classes with the approrpiate arguments.
 * 2) This will create an instance of CacheData which holds the indentifier and 'time last change'.
 * 3) Based on the cachedata the current caching engine will look for a match.
 * 4) If a match is found the object will be loaded from cache. ( continue with 7)
 * 5) If no match is found, an instance of the object will be created.
 * 6) On the newly created object the make(...) method will be called with the arguments passed to the initial create.
 * 7) The init(...) method will be called on the actual instance.
 * 8) The fully initialised object will be returned.
 * 
 * @see CacheableData
 * @see CacheableDirectory
 * @see CacheableFile
 * 
 * @author Jelle Voet
 * @version 0.1.0
 */
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
	 * This method is called when waking the object, and when loading it back from the cache.
	 * For example: creating a database connection should be done at this time.
	 */
	public function init() {}
	
	private $m_oData;

	/**
	 * 
	 * @param CacheData $oData
	 */
	public function __construct(CacheData $oData) {
		$this->m_oData = $oData;
	}

	/**
	 * 
	 * @return CacheData
	 */
	public function getCacheData() {
		return $this->m_oData;
	}

	/**
	 * This is ashort
	 * 
	 * @return mixed
	 */
	public function getConfiguration() {
		return $this->getCacheData()->getConfiguration();
	}

	/**
	 * 
	 * @param unknown $sKey
	 * @param string $mDefault
	 * @return Ambigous <mixed, multitype:, unknown>
	 */
	public function getConfig($sKey, $mDefault = null) {
		return $this->getCacheData()->getConfig($sKey, $mDefault);
	}

	/**
	 * This is a shorthand-method and calls update() on the current CacheData.
	 * 
	 * @example Cacheable::getCacheData()->update(Cacheable);
	 * @see CacheData::update()
	 */
	public function update() {
		return $this->getCacheData()->update($this);
	}
}

?>