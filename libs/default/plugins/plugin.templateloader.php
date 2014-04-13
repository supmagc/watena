<?php
require_includeonce(dirname(__FILE__) . '/../ipco/ipco.php');

/**
 * Template loader.
 * 
 * This plugin is a wrapper arround the IPCO system which parses the templates.
 * 
 * @author Jelle Voet
 * @version 0.2.0
 */
class TemplateLoader extends Plugin {
	
	/**
	 * Load the specified template-file.
	 *
	 * @param string $sTemplate
	 * @param IPCO_IContentParser $oContentParser
	 * @return IPCO_Processor
	 */
	public static function load($sTemplate, IPCO_IContentParser $oContentParser = null) {
		return TemplateFile::createTemplateFile($sTemplate, $oContentParser)->createTemplateClass();
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

/**
 * Helper class of the TemplateLoader plugin.
 * 
 * This class handles the caching of the IPCO output.
 * 
 * @author Jelle Voet
 * @version 0.2.0
 */
class TemplateFile extends CacheableFile implements IPCO_ICallbacks {

	private $m_sDataPath;
	private $m_sClassName;
	private $m_sExtends = null;
	
	/**
	 * Make the TemplateFile object.
	 * This will create an IPCO parser, feed ot the content of the CacheableFile, and save the
	 * IPCO output to a datafile that can be included.
	 * Additionally it will also keep a copy of the excat classname and parent class if any.
	 * 
	 * @see Cacheable::make()
	 * @param array $aMembers
	 */
	public function make(array $aMembers) {
		$oIpco = new IPCO($this->getContentParser(), $this);
		$this->m_sClassName = $oIpco->getTemplateClassName(parent::getFilePath());
		$this->m_sDataPath = 'IPCO/' . $this->m_sClassName . '.inc';
		$oFile = parent::getWatena()->getContext()->getDataFile($this->m_sDataPath);		
		$oParser = $oIpco->createParserFromFile(parent::getFilePath());
		$oFile->writeContent('<?php' . $oParser->parse() . '?>');
		$this->m_sExtends = $oParser->getExtendsFilePath();
	}
	
	/**
	 * Validates the saved datafile with the IPCO output.
	 * If none such file exists anymore, the file needs to be recompiled.
	 * 
	 * @see Cacheable::validate()
	 */
	public function validate() {
		return parent::getWatena()->getContext()->getDataFile($this->m_sDataPath)->exists();
	}

	/**
	 * Init the template by including it's class from the datafile.
	 * 
	 * @see Cacheable::init()
	 */
	public function init() {
		$oContentParser = $this->getContentParser();
		if(!$oContentParser || !($oContentParser instanceof IPCO_IContentParser)) {
			$this->getLogger()->error('The additional content parsers you provided for the selected template is not an IPCO_IContentParser.', array(
					'contentparser' => is_object($oContentParser) ? get_class($oContentParser) : 'None Object',
					'file' => parent::getFilePath()
				), 
				$this);
		}
		else {
			if($this->m_sExtends !== null) {
				TemplateFile::create($this->m_sExtends, parent::getConfiguration());
			}
			$oDataFile = parent::getWatena()->getContext()->getDataFile($this->m_sDataPath);
			$oDataFile->includeFileOnce();
		}
	}

	/**
	 * Get the actual local filepath matching the given template-name.
	 * 
	 * @see IPCO_ICallbacks::getFilePathForTemplate()
	 * @param string $sTemplate The template for which you want the filepath.
	 * @return string The filepath for the given template or false.
	 */
	public function getFilePathForTemplate($sTemplate) {
		return parent::getWatena()->getContext()->getLibraryFilePath('templates', $sTemplate);
	}

	/**
	 * The the template instance for the given filepath.
	 * 
	 * @see IPCO_ICallbacks::getTemplateClassForFilePath()
	 * @param string $sFilePath
	 * @return IPCO_Processor
	 */
	public function getTemplateClassForFilePath($sFilePath) {
		$oTemplateFile = TemplateFile::createTemplateFile($sFilePath, $this->getContentParser());
		if(empty($oTemplateFile)) {
			$this->getLogger()->error('Templatefile does not exists in any of the libraries, unable to load.', array('template' => $sFilePath), $this);
			return null;
		}		
		return $oTemplateFile->createTemplateClass();
	}
	
	/**
	 * Create the template-instance for this TemplateFile.
	 * 
	 * @return IPCO_Processor
	 */
	public function createTemplateClass() {
		$oIpco = new IPCO($this->getContentParser(), $this);
		$sClass = $this->m_sClassName;
		return new $sClass($oIpco, $this->getContentParser());
	}
	
	/**
	 * Retrieve th IPCO_IContentParser for this template.
	 * Thie value is retrieved from the configuration of the parent CacheableFile class.
	 * 
	 * @return IPCO_IContentParser
	 */
	private function getContentParser() {
		return $this->getConfig('contentparser', null);
	}
	
	/**
	 * Create a new, or load an existing TemplateFile instance.
	 * This is more handy version of the TemplateFile::create().
	 * 
	 * @see CacheableFile::create()
	 * @param string $sTemplate The name of the template which we will load.
	 * @param IPCO_IContentParser $oContentParser The optional parser for additional template-data.
	 * @return TemplateFile The loaded TemplateFile if $sTemplate points to a valid filepath, or null.
	 */
	public static function createTemplateFile($sTemplate, IPCO_IContentParser $oContentParser) {
		$sFilePath = parent::getWatena()->getContext()->getLibraryFilePath('templates', $sTemplate);
		if(!$sFilePath) {
			return null;
		}
		else {
			return self::create($sFilePath, array(), array('contentparser' => $oContentParser));
		}
	}
}

?>