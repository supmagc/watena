<?php

class Theme extends Cacheable {
	
	private $m_sName;
	private $m_sDirectory;
	
	public function init() {
		$this->m_sIdentifier = parent::getConfig('name', 'default');
		$this->m_sDirectory = parent::getWatena()->getPath('T:' . $this->m_sIdentifier);
	}
	
	public function wakeup() {
		// TODO !! continue from here on
		Theme::create('Theme', array('name' => $sName), 'THEME-NAME', Cacheable::EXP_NEVER, null, null, null, filemtime($sFilename));
	}
}

?>