<?php

class TemplateLoader extends Plugin {

	private $m_sDirectory;
	private $m_sExtension;
	
	public function init() {
		$this->m_sDirectory = parent::getWatena()->getPath(parent::getConfig('DIRECTORY', 'D:templates'));
		$this->m_sExtension = parent::getConfig('EXTENSION', 'tpl');
	}
	
	public function load($sTemplate) {
		if($this->m_sDirectory && $this->m_sExtension) {
			$sFile = $this->m_sDirectory . '/' . $sTemplate . '.' . $this->m_sExtension;
			$oTemplate = Cacheable::create('Template', array('file' => $sFile), "TL_$sTemplate", 5);
			return $oTemplate;
		}
	}
		
	/**
	 * Retrieve version information of this plugin.
	 * The format is an associative array as follows:
	 * 'major' => Major version (int)
	 * 'minor' => Minor version (int)
	 * 'build' => Build version (int)
	 * 'state' => Naming of the production state
	 */
	public function getVersion() {
		return array('major' => 0, 'minor' => 1, 'build' => 1, 'state' > 'dev');
	}
}

?>