<?php

class ZipFile extends Object {

	private $m_sPath;
	private $m_oZipper;
	
	public function __construct($sFilepath) {
		$this->m_sPath = $this->getWatena()->getPath($sFilepath, false);
		$this->m_oZipper = new ZipArchive();
		$this->m_oZipper->open($this->getFilepath(), ZipArchive::CREATE | ZipArchive::OVERWRITE);
	}
	
	public function __destruct() {
		$this->m_oZipper->close();
	}
	
	public function getFilepath() {
		return $this->m_sPath;
	}
	
	public function add($sName, $sPath) {
		$sPath = $this->getWatena()->getPath($sPath);
		if(is_file($sPath))
			return $this->m_oZipper->addFile($sPath, $sName);
		else if(is_dir($sPath)) {
			$aFiles = scandir($sPath);
			$bSuccess = $this->m_oZipper->addEmptyDir($sName);
			foreach($aFiles as $sFile) {
				if($sFile != '.' && $sFile != '..')
					$bSuccess = $bSuccess && $this->add($sName . '/' . $sFile, $sPath . '/' . $sFile);
			}
			return $bSuccess;
		}
		else
			return false;
	}
	
	public function create($sName, $sContent = null) {
		if($sContent === null)
			return $this->m_oZipper->addEmptyDir($sName);
		else
			return $this->m_oZipper->addFromString($sName, $sContent);
	}
	
	public function remove($sName) {
		return $this->m_oZipper->deleteName($sName);
	}
	
	public function extract($sPath, $sName = null) {
		$sPath = $this->getWatena()->getPath($sPath);
		if(is_writable($sPath))
			return $this->m_oZipper->extractTo($sPath, $sName);
		else
			return false;
	}
	
	public function setComment($sComment) {
		$this->m_oZipper->comment = $sComment;
	}
	
	public function getComment() {
		return $this->m_oZipper->comment;
	}
}

?>