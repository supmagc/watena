<?php namespace Watena\Libs\Base\Models;

use Watena\Core\Encoding;
use Watena\Core\Model;
use Watena\Core\Request;
use function Watena\Core\filechangetime;

/**
 * This model extracts a filename form the Request::path() data to serve
 * the specified file using a custom view.
 * 
 * Configuration:
 * - cacheduration: the duration the file may be cached by the browser. (default: 3600)
 * 
 * @author Jelle
 * @version 0.1.0
 */
class ThemeFileModel extends Model {
	
	private $m_sFileName = null;
	private $m_sFilePath = null;
	private $m_nFileChangeDate = 0;
	
	public function make(array $aMembers) {
		$sPath = Request::path();
		
		$nThemeIndex = Encoding::indexOf($sPath, '/theme/');
		if($nThemeIndex !== false) {
			$aParts = explode('/', Encoding::substring($sPath, $nThemeIndex + 1));
			if(count($aParts) > 2) {
				$this->m_sFileName = $aParts[1].'@theme/'.implode('/', array_slice($aParts, 2));
				$this->m_sFilePath = $this->getWatena()->getPath($this->m_sFileName);
				if($this->m_sFilePath) {
					$this->m_nFileChangeDate = filechangetime($this->m_sFilePath);
				}
				else {
					$this->getLogger()->warning('Unable to find the ThemeFileModel referenced file {filename}.', array('filename' => $this->m_sFileName));
				}
			}
			else {
				$this->getLogger()->warning('Unable to extract library-/theme-/file-parts from request data to create a ThemeFileModel-filename.', array('path' => $sPath));
			}
		}
		else {
			$this->getLogger()->warning('Unable to find the /theme/ offset in the request data to create a ThemeFileModel-filename.', array('path' => $sPath));
		}
	}

	public function validate() {
		return filechangetime($this->m_sFilePath) <= $this->m_nFileChangeDate;
	}
	
	public function getCacheDuration() {
		return $this->getConfig('cacheduration', 3600);
	}
	
	public function getLastModified() {
		return $this->m_nFileChangeDate;
	}
	
	public function getFileSize() {
		return filesize($this->m_sFilePath);
	}
	
	public function getFileContent() {
		return file_get_contents($this->m_sFilePath);
	}
	
	public function printFileContent() {
		echo file_get_contents($this->m_sFilePath);
	}
	
	public function getFileName() {
		return $this->m_sFileName;
	}
	
	public function getFilePath() {
		return $this->m_sFilePath;
	}
	
	public static function coarseCacheIdentifier() {
		return Request::path();
	}
}
