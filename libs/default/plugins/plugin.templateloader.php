<?php
require_once dirname(__FILE__) . '/../ipco/ipco.php';

class TemplateFile extends CacheableFile {

	private $m_sDataPath;
	private $m_sClassName;
	private $m_sExtends = null;
	
	public function init() {
		$oIpco = new IPCO(array($this, '_getTemplatePath'));
		$this->m_sClassName = $oIpco->getClassName(parent::getFilePath());
		$this->m_sDataPath = 'IPCO/' . $this->m_sClassName . '.inc';
		$oFile = parent::getWatena()->getContext()->getDataFile($this->m_sDataPath);		
		$oParser = $oIpco->createParserFromFile(parent::getFilePath());
		$oParser->setContentParser($this->_getContentParser());
		$oFile->writeContent('<?php' . $oParser->parse() . '?>');
		$this->m_sExtends = $oParser->getExtendsFilePath();
	}
	
	public function wakeup() {
		if($this->m_sExtends !== null) {
			TemplateFile::create($this->m_sExtends, parent::getConfiguration(), parent::getInstances());
		}
		$oDataFile = parent::getWatena()->getContext()->getDataFile($this->m_sDataPath);
		while(!$oDataFile->exists())
			$this->init();
		$oDataFile->includeFileOnce();
	}
	
	public function createTemplateClass() {
		$oIpco = new IPCO(array($this, '_getTemplatePath'));
		$sClass = $this->m_sClassName;
		return new $sClass($oIpco, $this->_getContentParser());
	}
	
	public function _getTemplatePath($sTemplate) {
		return parent::getWatena()->getContext()->getLibraryFilePath('templates', $sTemplate);
	}
	
	private function _getContentParser() {
		$oContentParser = parent::getInstance('contentparser', null);
		if($oContentParser !== null && !is_a($oContentParser, 'IPCO_IContentParser'))
			throw new WatCeption(
				'The additional content parsers you provided for the selected template is not an IPCO_IContentParser.', 
				array('contentparser' => is_object($oContentParser) ? get_class($oContentParser) : 'None Object', 'file' => parent::getFilePath()), 
				$this);
		return $oContentParser;
	}
}

class TemplateLoader extends Plugin {

	private $m_sDirectory;
	private $m_sExtension;
	
	public function init() {
	}

	/**
	 * Load the specified template-file.
	 * 
	 * @param string $sTemplate
	 * @param array $aParsers
	 * @throws WatCeption
	 */
	public function load($sTemplate, IPCO_IContentParser $oContentParser = null) {
		$sFilePath = parent::getWatena()->getContext()->getLibraryFilePath('templates', $sTemplate);
		if(!$sFilePath) throw new WatCeption('Templatefile does not exists in any of the libraries, unable to load.', array('template' => $sTemplate), $this);
		return TemplateFile::create($sFilePath, array(), array('contentparser' => $oContentParser))->createTemplateClass();
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