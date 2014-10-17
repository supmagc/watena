<?php

class MinifieCssView extends View {
	
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
		}
		
		if(false)
			$oDataFile->printContent();
		else 
			$oModel->printFileContent();
	}
}

?>