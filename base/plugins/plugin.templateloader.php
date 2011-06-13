<?php

includeLibrary('ipco', array('ipco', 'ipco_base', 'ipco_processor', 'ipco_componentwrapper'));

class TemplateFile extends CacheableFile {

	private $m_sDataPath;
	
	public function init() {
		$oIpco = new IPCO();
		$this->m_sDataPath = 'IPCO/' . $oIpco->getClassName(parent::getFilePath()) . '.inc';
		$oFile = parent::getWatena()->getContext()->getDataFile($this->m_sDataPath);		
		$oParser = $oIpco->getParser(parent::getFilePath(), parent::getFileData());
		$oFile->writeContent($oParser->parse());
	}
	
	public function wakeup() {
		parent::getWatena()->getContext()->getDataFile($this->m_sDataPath)->includeFileOnce();
	}
}

class TemplateLoader extends Plugin {

	private $m_oIpco;
	
	private $m_sDirectory;
	private $m_sExtension;
	
	public function init() {
		$this->m_oIpco = new IPCO(
			parent::getWatena()->getPath(parent::getConfig('SOURCE_DIRECTORY', 'T:ipco_templates')),
			parent::getConfig('SOURCE_EXTENSION', 'tpl'));
		
		$this->m_sDirectory = parent::getWatena()->getPath(parent::getConfig('DIRECTORY', 'D:ipco_templates'));
		$this->m_sExtension = parent::getConfig('EXTENSION', 'tpl');
	}
	
	public function load($sTemplate) {
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

	public static function getRequirements() {
		return array('plugins' => 'ThemeManager');
	}
}

?>