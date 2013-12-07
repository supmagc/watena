<?php

class DataFile extends Object {

	private $m_sPath;
	private $m_sFullPath;
	private $m_oZipfile;
	
	public function __construct($sPath) {
		parent::__construct();
		
		$this->m_sPath = $sPath;
		
		$sPath = Encoding::replace(array('/../', '/..', '..'), '', $sPath);
		$sPath = Encoding::replace('\\', '/', $sPath);		
		$this->m_sFullPath = PATH_DATA . '/' . $sPath . '.df';
	}
	
	public function exists() {
		return file_exists($this->m_sFullPath);
	}
	
	public function getPath() {
		return $this->m_sPath;
	}
	
	public function getZipfile() {
		if(!$this->m_oZipfile)
			$this->m_oZipfile = new ZipFile($this->m_sFullPath);
		return $this->m_oZipfile;
	}
	
	public function getTimestamp() {
		return $this->exists() ? filemtime($this->m_sFullPath) : 0;
	}
	
	public function readContent() {
		return $this->exists() ? file_get_contents($this->m_sFullPath, false) : null;
	}
	
	public function writeContent($mContent) {
		$sDir = dirname($this->m_sFullPath);
		if(!file_exists($sDir))
			mkdir($sDir, 0775, true);
		file_put_contents($this->m_sFullPath, $mContent);
		chmod($this->m_sFullPath, 0664);
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