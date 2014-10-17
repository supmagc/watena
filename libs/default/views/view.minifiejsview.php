<?php

class MinifieJsView extends View {
	
	private $m_nOriginalSize;
	private $m_nMinifiedSize;
	
	public function requiredModelType() {
		return 'ThemeFileModel';
	}
	
	public function headers(Model $oModel = null) {
		$this->setContentType('application/javascript');
	}
	
	public function render(Model $oModel = null) {
		$oDataFile = new DataFile('MinifiedJs/'.DataFile::makeNameSafe($oModel->getFilePath()));
		
		if($oDataFile->getTimestamp() < $oModel->getLastModified()) {
			$this->getWatena()->getContext()->loadPlugin('JShrink');
			$oDataFile->writeContent(JShrink::minifie($oModel->getFileContent()));
			$this->m_nOriginalSize = $oModel->getFileSize();
			$this->m_nMinifiedSize = $oDataFile->getFileSize();
			$this->getCacheData()->update($this);
			
			$this->getLogger()->info("Minified the js-file {path} from {original} bytes to {minified} bytes.", array('path' => $oModel->getFilePath(), 'original' => $this->m_nOriginalSize, 'minified' => $this->m_nMinifiedSize));
		}
		else {
			$this->getLogger()->info("Served an earlier cached js-file {path} from {original} bytes to {minified} bytes.", array('path' => $oModel->getFilePath(), 'original' => $this->m_nOriginalSize, 'minified' => $this->m_nMinifiedSize));
		}
		
		if(true)
			$oDataFile->printContent();
		else 
			$oModel->printFileContent();
	}
}

?>