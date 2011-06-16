<?php

includeLibrary('ipco', array(
	'ipco', 
	'ipco_base', 
	'ipco_processor',
	'ipco_parsersettings',
	'ipco_parser', 
	'ipco_expression', 
	'ipco_exception', 
	'ipco_expressionexception', 
	'ipco_componentwrapper'
));

class TemplateFile extends CacheableFile {

	private $m_sDataPath;
	private $m_sClassName;
	private $m_oIpco;
	
	public function init() {
		$this->m_oIpco = new IPCO();
		$this->m_sClassName = $this->m_oIpco->getClassName(parent::getFilePath());
		$this->m_sDataPath = 'IPCO/' . $this->m_sClassName . '.inc';
		$oFile = parent::getWatena()->getContext()->getDataFile($this->m_sDataPath);		
		$oParser = $this->m_oIpco->createParserFromFile(parent::getFilePath());
		$oFile->writeContent('<?php' . $oParser->parse() . '?>');
	}
	
	public function wakeup() {
		$oDataFile = parent::getWatena()->getContext()->getDataFile($this->m_sDataPath);
		while(!$oDataFile->exists())
			$this->init();
		$oDataFile->includeFileOnce();
	}
	
	public function getTemplateClass() {
		$sClass = $this->m_sClassName;
		return new $sClass($this->m_oIpco);
	}
}

class TemplateLoader extends Plugin {

	private $m_oIpco;
	
	private $m_sDirectory;
	private $m_sExtension;
	
	public function init() {
		$this->m_oIpco = new IPCO();
	}
	
	public function load($sTemplate) {
		$sFilePath = parent::getWatena()->getContext()->getProjectFilePath('templates', $sTemplate);
		if(!$sFilePath) throw new WatCeption('Templatefile does not exists in any of the libraries.', array('template' => $sTemplate), $this);
		return TemplateFile::create($sFilePath);
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