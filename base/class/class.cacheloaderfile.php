<?php

class CacheLoaderFile extends CacheLoader {
	
	public function __construct($sClassName, $sFileName) {
		$sFilePath = parent::getWatena()->getPath($sFileName);
		if(!$sFilePath || !is_readable($sFilePath)) {
			$this->getLogger()->error('CacheLoaderFile cannot load \'{class}\' as the required directory \'{file}\' is not readable.', array('class' => $sClassName, 'file' => $sFileName));
		}
		parent::__construct($sClassName, array('m_sFileName' => $sFileName, 'm_sFilePath' => $sFilePath), 'CacheableFile');
		$this->addPathDependency($sFilePath);
	}
}

?>