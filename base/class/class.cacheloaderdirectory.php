<?php

class CacheLoaderDirectory extends CacheLoader {
	
	public function __construct($sClassName, $sDirectoryName) {
		$sDirectoryPath = parent::getWatena()->getPath($sDirectoryName);
		if(!$sDirectoryPath || !is_readable($sDirectoryPath)) {
			$this->getLogger()->error('CacheLoaderDirectory cannot load \'{class}\' as the required directory \'{directory}\' is not readable.', array('class' => $sClassName, 'directory' => $sDirectoryName));
		}
		parent::__construct($sClassName, array('m_sDirectoryName' => $sDirectoryName, 'm_sDirectoryPath' => $sDirectoryPath), 'CacheableDirectory');
		$this->addPathDependency($sDirectoryPath);
	}
}

?>