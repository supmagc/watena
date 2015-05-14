<?php

abstract class Model extends CacheableData {

	/**
	 * Get when the content of this model was last updated.
	 * 
	 * @return int A timestamp for the last modified time.
	 */
	public function getLastModified() {return 0;}
	
	/**
	 * Get how long the content of this model may reside in a cache.
	 * 
	 * @return int Amount of seconds the cache remains valid.
	 */
	public function getCacheDuration() {return 0;}
	
	/**
	 * Get a unique tag for caching the current model data.
	 * 
	 * @return string|null the 'ETag' value for the model data.
	 */
	public function getCacheTag() {return null;}
	
	/**
	 * Does the data of this model supports compression
	 * 
	 * @return boolean
	 */
	public function compressionSupport() {return true;}
	
	/**
	 * Callback to prepare the data of this model for processing.
	 */
	public function prepare() {}
}
