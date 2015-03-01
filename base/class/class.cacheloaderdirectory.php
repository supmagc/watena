<?php

class CacheLoaderDirectory extends CacheLoader {
	
	public function __construct($sClassName, $sDirectoryName, array $aMembers = array()) {
		$sDirectoryPath = parent::getWatena()->getPath($sDirectoryName);
		if(isset($aMembers['m_sDirectoryName'])) {
			$this->getLogger()->warning('You shouldn\'t set a members variable for m_sDirectoryName as {directoryname} for this CacheLoaderDirectory.', array('directoryname' => $sDirectoryName, 'directorypath' => $sDirectoryPath));
		}
		if(isset($aMembers['m_sDirectoryPath'])) {
			$this->getLogger()->warning('You shouldn\'t set a members variable for m_sDirectoryPath as {directorypath} for this CacheLoaderDirectory.', array('directoryname' => $sDirectoryName, 'directorypath' => $sDirectoryPath));
		}
		$aMembers['m_sDirectoryName'] = $sDirectoryName;
		$aMembers['m_sDirectoryPath'] = $sDirectoryPath;
		parent::__construct($sClassName, $aMembers, 'CacheableDirectory');
		if(!$this->addPathDependency($sDirectoryPath)) {
			throw new WatCeption('CacheLoaderDirectory cannot load \'{class}\' as the required directory \'{directory}\' is not readable.', array('class' => $sClassName, 'directory' => $sDirectoryName));
		}
	}
}
