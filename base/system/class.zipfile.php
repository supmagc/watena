<?php

class ZipFile extends Object {
	
	public function __construct($sFilepath) {
		$this->m_sPath = $this->getWatena()->getPath($sFilepath, false);
	}
	
	public function add($sPath, $sName) {
		
	}
	
	public function remove($sName) {
		
	}
	
	public function extract($sPath, $sName) {
		
	}
	
	public function setComment($sComment) {
		
	}
	
	public function getComment() {
		
	}
}

?>