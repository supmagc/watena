<?php namespace Watena\Core;

class CacheLoaderFile extends CacheLoader {
	
	public function __construct($sClassName, $sFileName, array $aMembers = array()) {
		$sFilePath = parent::getWatena()->getPath($sFileName);
		if(isset($aMembers['m_sFileName'])) {
			$this->getLogger()->warning('You shouldn\'t set a members variable for m_sFileName as {filename} for this CacheLoaderFile.', array('filename' => $sFileName, 'filepath' => $sFilePath));
		}
		if(isset($aMembers['m_sFilePath'])) {
			$this->getLogger()->warning('You shouldn\'t set a members variable for m_sFilePath as {filepath} for this CacheLoaderFile.', array('filename' => $sFileName, 'filepath' => $sFilePath));
		}
		$aMembers['m_sFileName'] = $sFileName;
		$aMembers['m_sFilePath'] = $sFilePath;
		parent::__construct($sClassName, $aMembers, __NAMESPACE__.'\CacheableFile');
		if(!$this->addPathDependency($sFilePath)) {
			throw new WatCeption('CacheLoaderFile cannot load \'{class}\' as the required directory \'{file}\' is not readable.', array('class' => $sClassName, 'file' => $sFileName));
		}
	}
}
