<?php
require_once dirname(__FILE__) . '/ipco_base.php';
require_once dirname(__FILE__) . '/ipco_processor.php';
require_once dirname(__FILE__) . '/ipco_parsersettings.php';
require_once dirname(__FILE__) . '/ipco_icontentparser.php';
require_once dirname(__FILE__) . '/ipco_contentparserpart.php';
require_once dirname(__FILE__) . '/ipco_parser.php';
require_once dirname(__FILE__) . '/ipco_expression.php';
require_once dirname(__FILE__) . '/ipco_exception.php';
require_once dirname(__FILE__) . '/ipco_expressionexception.php';
require_once dirname(__FILE__) . '/ipco_componentwrapper.php';

/**
 * Inline php compilator.
 * 
 * @author Jelle Voet
 * @version 0.1.0 Beta
 */
class IPCO {

	private $m_cbTemplateToFile;
	
	public function __construct($cbTemplateToFile) {
		$this->setTemplateToFileCallback($cbTemplateToFile);
	}
	
	public function setTemplateToFileCallback($cbTemplateToFile) {
		if(is_callable($cbTemplateToFile))
			$this->m_cbTemplateToFile = $cbTemplateToFile;
		else
			throw new IPCO_Exception('The Template-To-File callback you specified is not callable.', IPCO_Exception::TEMPLATETOFILE_UNCALLABLE);
	}
	
	public function getTemplateToFileCallback() {
		return $this->m_cbTemplateToFile;
	}
	
	/**
	 * Create an IPCO parser from the given content.
	 * You also need to provide an identifier that'll be used as generated class-name.
	 * 
	 * @param string $sIdentifier
	 * @param string $sContent
	 * 
	 * @return IPCO_Parser
	 */
	public function createParserFromContent($sIdentifier, &$sContent) {
		return new IPCO_Parser($sIdentifier, $sContent, $this);
	}

	/**
	 * Create an IPCO parser based on the content of the given filepath.
	 * As Identifier the filename will be used.
	 * 
	 * @param string $sFilePath
	 * 
	 * @return IPCO_Parser
	 */
	public function createParserFromFile($sFilePath) {
		$sContent = file_get_contents($sFilePath);
		return $this->createParserFromContent($sFilePath, $sContent, $this);
	}
	
	public function createParserFromTemplate($sTemplate) {
		$sFilePath = call_user_func($this->m_cbTemplateToFile, $sTemplate);
		return $this->createParserFromFile($sFilePath);
	}
	
	/**
	 * Generate a valid classname for the given identifier.
	 * 
	 * @param string $sIdentifier
	 * 
	 * @return string
	 */
	public function getClassName($sIdentifier) {
		$sIdentifier = Encoding::toLower($sIdentifier);
		return 'IPCO_Compiled_' . Encoding::regReplace('[-/\\\\.: ]', '_', $sIdentifier);
	}
}

?>