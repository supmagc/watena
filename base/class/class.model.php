<?php

abstract class Model extends CacheableData {
	
	public function getLastModified() {return 0;}
	
	public function getETag() {return null;}
}

?>