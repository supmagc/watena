<?php
/**
 * Serve a minified version of a specified css file to the end user.
 * This view expetcs data from a ThemeFileModel-instance.
 * 
 * Configuration:
 * - minifieifdebug: Indicate if we still should server minified files when debugging. (default: false)
 *
 * @author Jelle Voet
 * @version 0.1.0
 */
class MinifieCssView extends View {

	private $m_nOriginalSize;
	private $m_nMinifiedSize;
	private $m_bMinifieIfDebug = false;

	public final function make(array $aMembers) {
		$this->m_bMinifieIfDebug = array_value($aMembers, 'minifieifdebug', false);
	}
	
	public function requiredModelType() {
		return 'ThemeFileModel';
	}
	
	public function headers(Model $oModel = null) {
		$this->setContentType('text/css');
	}
	
	public function render(Model $oModel = null) {
		if(isDebug() && !$this->m_bMinifieIfDebug) {
			$oModel->printFileContent();
		}
		else {
			$oDataFile = new DataFile('MinifiedCss/'.DataFile::makeNameSafe($oModel->getFilePath()));
			
			if($oDataFile->getTimestamp() >= $oModel->getLastModified()) {
				$this->getLogger()->info("Served an earlier cached css-file {path}. (from {original} bytes to {minified} bytes)", array('path' => $oModel->getFileName(), 'original' => $this->m_nOriginalSize, 'minified' => $this->m_nMinifiedSize));
				$oDataFile->printContent();
			}
			else {
				$sData = $oModel->getFileContent();
				$sData = Encoding::regReplace('/\*[^*]*\*+([^/][^*]*\*+)*/', '', $sData);
				$sData = Encoding::replace(array("\r\n", "\r", "\n", "\t", '  ', '   '), '', $sData);
				$oDataFile->writeContent($sData);
				$this->m_nOriginalSize = $oModel->getFileSize();
				$this->m_nMinifiedSize = $oDataFile->getFileSize();
				$this->getCacheData()->update($this);
					
				$this->getLogger()->info("Minified the css-file {path}. (from {original} bytes to {minified} bytes)", array('path' => $oModel->getFileName(), 'original' => $this->m_nOriginalSize, 'minified' => $this->m_nMinifiedSize));
				echo $sData;
			}
		}
	}
}

?>