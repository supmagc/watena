<?php

class MinifieCssView extends View {

	private $m_nOriginalSize;
	private $m_nMinifiedSize;
	
	public function requiredModelType() {
		return 'ThemeFileModel';
	}
	
	public function headers(Model $oModel = null) {
		$this->setContentType('text/css');
	}
	
	public function render(Model $oModel = null) {
		$oDataFile = new DataFile('MinifiedCss/'.DataFile::makeNameSafe($oModel->getFilePath()));
		
		if($oDataFile->getTimestamp() < $oModel->getLastModified()) {
			$sData = $oModel->getFileContent();
			$sData = Encoding::regReplace('/\*[^*]*\*+([^/][^*]*\*+)*/', '', $sData);
			$sData = Encoding::replace(array("\r\n", "\r", "\n", "\t", '  ', '   '), '', $sData);
			$oDataFile->writeContent($sData);
			$this->m_nOriginalSize = $oModel->getFileSize();
			$this->m_nMinifiedSize = $oDataFile->getFileSize();
			$this->getCacheData()->update($this);
			
			$this->getLogger()->info("Minified the css-file {path} from {original} bytes to {minified} bytes.", array('path' => $oModel->getFilePath(), 'original' => $this->m_nOriginalSize, 'minified' => $this->m_nMinifiedSize));
		}
		else {
			$this->getLogger()->info("Served an earlier cached css-file {path} from {original} bytes to {minified} bytes.", array('path' => $oModel->getFilePath(), 'original' => $this->m_nOriginalSize, 'minified' => $this->m_nMinifiedSize));
		}
					
		if(false)
			$oDataFile->printContent();
		else 
			$oModel->printFileContent();
	}
}

?>