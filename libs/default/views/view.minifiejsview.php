<?php

class MinifieJsView extends View {
	
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
		}
		
		if(false)
			$oDataFile->printContent();
		else 
			$oModel->printFileContent();
	}
}

?>