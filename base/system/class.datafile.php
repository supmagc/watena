<?php

class DataFile extends Object {

	private $m_sPath;
	private $m_sFullPath;
	
	public function __construct($sPath) {
		parent::__construct();
		
		$this->m_sPath = $sPath;
		
		$sPath = Encoding::stringReplace(array('/../', '/..', '..'), '', $sPath);
		$sPath = Encoding::stringReplace('\\', '/', $sPath);		
		$this->m_sFullPath = PATH_DATA . '/' . $sPath . '.df';
	}
	
	public function exists() {
		return file_exists($this->m_sFullPath);
	}
	
	public function getPath() {
		return $this->m_sPath;
	}
	
	public function getTimestamp() {
		return $this->exists() ? filemtime($this->m_sFullPath) : 0;
	}
	
	public function readContent() {
		return $this->exists() ? file_get_contents($this->m_sFullPath, false) : null;
	}
	
	public function writeContent($mContent) {
		mkdir(dirname($this->m_sFullPath), 0777, true);
		file_put_contents($this->m_sFullPath, $mContent);
		chmod($this->m_sFullPath, 0777);
	}
	
	public function includeFile() {
		if($this->exists()) {
			return include $this->m_sFullPath;
		}
	}
	
	public function includeFileOnce() {
		if($this->exists()) {
			return include_once $this->m_sFullPath;
		}
	}
}

?>