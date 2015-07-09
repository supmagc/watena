<?php namespace Watena\Core;

class FilePermissionException extends WatCeption {
	
	private $m_sFilepath;
	
	public function __construct($sFilepath, Object $oContext = null) {
		parent::__construct('There was a file-permission exception for: {file}', array('file' => $sFilepath), $oContext);
		$this->m_sFilepath = $sFilepath;
	}
	
	public function getFilepath() {
		return $this->m_sFilepath;
	}
}
