<?php

class Upload extends Object {
	
	private $m_sFile;
	private $m_aFile;
	
	public final function __construct($sFile) {
		$this->m_sFile = $sFile;
		if(isset($_FILES[$sFile]))
			$this->m_aFile = $_FILES[$sFile];
	}
	
	public final function exists() {
		return is_array($this->m_aFile) && isset($this->m_aFile['tmp_name']) && file_exists($this->m_aFile['tmp_name']);
	}
	
	public final function getLength() {
		return ($this->exists() && isset($this->m_aFile['size'])) ? $this->m_aFile['size'] : false;
	}
	
	public final function getContent() {
		return $this->exists() ? file_get_contents($this->m_aFile['tmp_name']) : false;
	}
	
	public final function getFilesize() {
		return $this->exists() ? filesize($this->m_aFile['tmp_name']) : false;
	}
	
	public final function getMimeType() {
		return ($this->exists() && isset($this->m_aFile['type'])) ? $this->m_aFile['type'] : false;
	}
	
	public final function getName() {
		return ($this->exists() && isset($this->m_aFile['name'])) ? $this->m_aFile['name'] : false;
	}
	
	public final function getFilename() {
		if($this->getName()) {
			$aMatches = array();
			$aPositions = array();
			if(Encoding::regFind('^(.*)(\.[a-zA-Z0-9]+)$', $this->getName(), $aMatches, $aPositions))
				return $aMatches[1];
			else
				return $this->getName();
		}
		return false;
	}
	
	public final function getExtension() {
		if($this->getName()) {
			$aMatches = array();
			$aPositions = array();
			if(Encoding::regFind('^(.*)(\.([a-zA-Z0-9]+))$', $this->getName(), $aMatches, $aPositions))
				return $aMatches[3];
			else
				return $this->getName();
		}
		return false;
	}
	
	public final function getError() {
		return ($this->exists() && isset($this->m_aFile['error'])) ?($this->m_aFile['error'] ?: false) : false;
	}
	
	public final function getErrorMessage() {
		switch($this->getError()) {
			case UPLOAD_ERR_OK: return false;
			case UPLOAD_ERR_INI_SIZE: return 'The uploaded file exceeds the upload_max_filesize directive in php.ini.';
			case UPLOAD_ERR_FORM_SIZE: return 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.';
			case UPLOAD_ERR_PARTIAL: return 'The uploaded file was only partially uploaded.';
			case UPLOAD_ERR_NO_FILE: return 'No file was uploaded.';
			case UPLOAD_ERR_NO_TMP_DIR: return 'Missing a temporary folder.';
			case UPLOAD_ERR_CANT_WRITE: return 'Failed to write file to disk.';
			case UPLOAD_ERR_EXTENSION: return 'A PHP extension stopped the file upload.';
			default : return 'Unknown upload error.';
		}
	}
	
	public final function move($sDestination, $bOverwrite = false) {
		if($this->exists()) {
			$sDestination = $this->getWatena()->getPath($sDestination, false);
			if(is_dir($sDestination)) {
				$sDirectorypath = $sDestination;
				$sFilepath = $sDestination . '/' . $this->getName();
			}
			else {
				$sDirectorypath = dirname($sDestination);
				$sFilepath = $sDestination;
			}
				
			if(!is_dir($sDirectorypath)) {
				mkdir($sDirectorypath, 0775);
			}
			
			if(!file_exists($sFilepath) || $bOverwrite) {
				if(@move_uploaded_file($this->m_aFile['tmp_name'], $sFilepath)) {
					chmod($sFilepath, 0664);
					return true;
				}
			}
		}
		return false;
	}
}

?>