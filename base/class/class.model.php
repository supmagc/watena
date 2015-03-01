<?php

abstract class Model extends CacheableData {

	public function getLastModified() {return 0;}
	
	public function getCacheDuration() {return 0;}
	
	public function getCacheTag() {return null;}
	
	public function compressionSupport() {return true;}
	
	public function prepare() {}
}
