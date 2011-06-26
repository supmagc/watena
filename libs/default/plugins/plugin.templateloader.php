<?php
require_once dirname(__FILE__) . '/../ipco/ipco.php';

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
		$aParsers = parent::getConfig('parsers', array());
		foreach($aParsers as $cbParser)
			$oParser->addParserCallback($cbParser);
		$oFile->writeContent('<?php' . $oParser->parse() . '?>');
	}
	
	public function wakeup() {
		$oDataFile = parent::getWatena()->getContext()->getDataFile($this->m_sDataPath);
		while(!$oDataFile->exists())
			$this->init();
		$oDataFile->includeFileOnce();
	}
	
	public function createTemplateClass() {
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

	/**
	 * Load the specified template-file.
	 * If required, you can specify an array with callbacks.
	 * Make sure these callbacks point to static or global functions.
	 * 
	 * @param unknown_type $sTemplate
	 * @param array $aTemplateContentSearchers
	 * @throws WatCeption
	 */
	public function load($sTemplate, array $aParsers = array()) {
		$sFilePath = parent::getWatena()->getContext()->getLibraryFilePath('templates', $sTemplate);
		if(!$sFilePath) throw new WatCeption('Templatefile does not exists in any of the libraries.', array('template' => $sTemplate), $this);
		return TemplateFile::create($sFilePath, array('parsers' => $aParsers));
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