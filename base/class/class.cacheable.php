<?php

/**
 * Base class for caching behaviour.
 * The caching protocol works as follows:
 *  1) The user calls the create on one of the child classes with the approrpiate arguments.
 *  2) This will create an instance of CacheLoader which will control the required dependencies.
 *  3) The loader will generate the corretc identifiersand see if an existing object can be loaded.
 *  4) If such and object is found, it will be loaded from cache (continue 8) and the provided config will be injected.
 *  5) The freshly loaded object will be given a last chance to invalidate itself during the validate(...) call.
 *  6) If such an object is unknown a CacheData instance containing the identifiers will be created.
 *  7) The system will try to find a match for the given members and set them if applicable.
 *  8) On the newly created object the make(...) method will be called with the arguments passed to the initial create.
 *  9) The init(...) method will be called on the actual instance.
 * 10) The fully initialised object will be returned.
 * 
 * @see CacheData
 * @see CacheLoader
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
	
	/**
	 * This method gets called before creating the cacheable instance.
	 * It serves to expand the cache identifier, and thus allows for a more fine-grained
	 * cache identifier based on custom data.
	 * If the cacheable object for example requires some request data, you could it it here.
	 * 
	 * Since this function gets called before creating the instance, it should always be static!
	 * 
	 * @return mixed
	 */
	public static function coarseCacheIdentifier() {}
	
	/**
	 * 
	 * @var CacheData
	 */
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
	 * This is a shorthand for the configuration data in the CacheData
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
	 * @return string|number|boolean|array
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
		$this->getCacheData()->update($this);
	}
}
