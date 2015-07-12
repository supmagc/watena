<?php namespace Watena\Libs\Base\Views;
/**
 * Serve a minified version of a specified javascript file to the end user.
 * This view expetcs data from a ThemeFileModel-instance.
 * Configuration:
 * - usegoogleclosure: Try to minifie using the google closure REST-API. (default: true)
 * - usejshrinkplugin: Try to minifie using the local JShrink plugin. (default: true)
 * - minifieifdebug: Indicate if we still should server minified files when debugging. (default: false)
 * 
 * @author Jelle Voet
 * @version 0.1.0
 */
class MinifieJsView extends View {
	
	private $m_nOriginalSize;
	private $m_nMinifiedSize;
	private $m_bUseGoogleClosure = true;
	private $m_bUseJShrinkPlugin = true;
	private $m_bMinifieIfDebug = false;
	
	public final function make(array $aMembers) {
		$this->m_bUseGoogleClosure = array_value($aMembers, 'usegoogleclosure', true);
		$this->m_bUseJShrinkPlugin = array_value($aMembers, 'usejshrinkplugin', true);
		$this->m_bMinifieIfDebug = array_value($aMembers, 'minifieifdebug', false);
	}
	
	public function requiredModelType() {
		return 'ThemeFileModel';
	}
	
	public function headers(Model $oModel = null) {
		$this->setContentType('application/javascript');
	}
	
	public function render(Model $oModel = null) {
		if((isDebug() && !$this->m_bMinifieIfDebug) || (
				!($this->m_bUseGoogleClosure && $this->_tryGoogleClosure($oModel)) && 
				!($this->m_bUseJShrinkPlugin && $this->_tryJShrinkPlugin($oModel))
			)) {
			$oModel->printFileContent();
		}
	}
	
	private function _tryGoogleClosure(ThemeFileModel $oModel) {
		$oDataFile = new DataFile('MinifiedJs/CLOSURE_'.DataFile::makeNameSafe($oModel->getFilePath()));
		$bReturn = false;
		
		if($oDataFile->getTimestamp() >= $oModel->getLastModified()) {
			$this->getLogger()->info("Served the earlier by Google Closure minified js-file {path}. (from {original} bytes to {minified} bytes)", array('path' => $oModel->getFileName(), 'original' => $this->m_nOriginalSize, 'minified' => $this->m_nMinifiedSize));
			$oDataFile->printContent();
			$bReturn = true;
		}
		else {
			$oRequest = new WebRequest(new Url('http://closure-compiler.appspot.com/compile'), 'POST');
			$oRequest->addField('js_code', $oModel->getFileContent());
			$oRequest->addField('compilation_level', 'SIMPLE_OPTIMIZATIONS');
			$oRequest->addField('output_format', 'json');
			$oRequest->addField('warning_level', 'DEFAULT');
			$oRequest->addField('language', 'ECMASCRIPT3');
			$oRequest->setPostDataAppend('output_info=compiled_code&output_info=warnings&output_info=errors&output_info=statistics');
			
			$oResponse =  $oRequest->send();
			if($oResponse->getHttpCode() == 200) {
				$aResponse = json_decode($oResponse->getContent(), true); 
				
				if(isset($aResponse['serverErrors']) && count($aResponse['serverErrors']) > 0) {
					foreach($aResponse['serverErrors'] as $aServerError) {
						$aServerError['file'] = $oModel->getFileName();
						$this->getLogger()->warning('Google Closure returned a server-error for {file}: {error}', $aServerError);
					}
				}
				else if(isset($aResponse['errors']) && count($aResponse['errors']) > 0) {
					foreach($aResponse['errors'] as $aError) {
						$aError['file'] = $oModel->getFileName();
						$this->getLogger()->warning('Google Closure returned an error when minifying {file}: {error}', $aError);
					}
				}
				else if(isset($aResponse['warnings']) && count($aResponse['warnings']) > 0) {
					foreach($aResponse['warnings'] as $aWarning) {
						$aWarning['file'] = $oModel->getFileName();
						$this->getLogger()->warning('Google Closure returned a warning when minifying {file}: {warning}', $aWarning);
					}
				}
				else if(!isset($aResponse['compiledCode'])) {
					$aResponse['file'] = $oModel->getFileName();
					$this->getLogger()->warning('Google Closure succeeded to minifie {file}, but didn\'t return any content.', $aResponse);
				}
				else {
					$oDataFile->writeContent($aResponse['compiledCode']);
					$this->m_nOriginalSize = array_value($aResponse, array('statistics', 'originalSize'), 0);
					$this->m_nMinifiedSize = array_value($aResponse, array('statistics', 'compressedSize'), 0);
					$this->getCacheData()->update($this);
					
					$this->getLogger()->info("Google Closure minified the js-file {path}. (from {original} bytes to {minified} bytes)", array('path' => $oModel->getFileName(), 'original' => $this->m_nOriginalSize, 'minified' => $this->m_nMinifiedSize));
					echo $aResponse['compiledCode'];
					$bReturn = true;
				}
			}
			else {
				$this->getLogger()->warning('Unable to contact the Coogle Closure REST-API.', array('code' => $oResponse->getHttpCode()));
			}
		}
		return $bReturn;
	}
	
	private function _tryJShrinkPlugin(ThemeFileModel $oModel) {
		$oDataFile = new DataFile('MinifiedJs/JSHRINK_'.DataFile::makeNameSafe($oModel->getFilePath()));
		$bReturn = false;
		
		if($oDataFile->getTimestamp() >= $oModel->getLastModified()) {
			$this->getLogger()->info("Served the earlier by JShrink minified js-file {path}. (from {original} bytes to {minified} bytes)", array('path' => $oModel->getFileName(), 'original' => $this->m_nOriginalSize, 'minified' => $this->m_nMinifiedSize));
		}
		else {
			if(watena()->getContext()->loadPlugin('JShrink')) {
				$sMinified = JShrink::minifie($oModel->getFileContent());
				$this->getLogger()->info("JShrink minified the js-file {path}. (from {original} bytes to {minified} bytes)", array('path' => $oModel->getFileName(), 'original' => $this->m_nOriginalSize, 'minified' => $this->m_nMinifiedSize));
				$oDataFile->writeContent($sMinified);
				echo $sMinified;
				$bReturn = true;
			}
			else {
				$this->getLogger()->warning('The JShrin plugin could not be found to minifie {file}.', array('file' => $oModel->getFileName()));
			}
		}

		return $bReturn;
	}
}

?>