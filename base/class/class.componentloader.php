<?php

class ComponentLoader extends CacheableFile {
	
	public static final function create($sClass, $sPath) {
		return CacheableFile::create($sPath, array('path' => $sPath));
	}
}

?>