<?php
require_once dirname(__FILE__) . '/ipco_base.php';
require_once dirname(__FILE__) . '/ipco_processor.php';
require_once dirname(__FILE__) . '/ipco_parsersettings.php';
require_once dirname(__FILE__) . '/ipco_icallbacks.php';
require_once dirname(__FILE__) . '/ipco_icontentparser.php';
require_once dirname(__FILE__) . '/ipco_contentparserpart.php';
require_once dirname(__FILE__) . '/ipco_parserregion.php';
require_once dirname(__FILE__) . '/ipco_parser.php';
require_once dirname(__FILE__) . '/ipco_expression.php';
require_once dirname(__FILE__) . '/ipco_exception.php';
require_once dirname(__FILE__) . '/ipco_expressionexception.php';
require_once dirname(__FILE__) . '/ipco_componentwrapper.php';

/**
 * Inline php compilator.
 * 
 * This library takes a template file, parses it, and returns valid and optimised php code.
 * Choosing to recompile a file, saving the result, or re-using a previous version is not included.
 * Thus you need to make your own wrapper to handle the caching of the IPCO output.
 * 
 * @author Jelle Voet
 * @version 0.1.0 Beta
 */
class IPCO {

	private $m_oContentParser;
	private $m_oCallbacks;
	
	public function __construct(IPCO_IContentParser $oContentParser = null, IPCO_ICallbacks $oCallbacks = null) {
		$this->setContentParser($oContentParser);
		$this->setCallbacks($oCallbacks);
	}
	
	public function createParserFromContent($sIdentifier, &$sContent) {
		return new IPCO_Parser($sIdentifier, $sContent, $this);
	}
	
	public function createParserFromTemplate($sTemplate) {
		$sFilePath = $this->getFilePathForTemplate($sTemplate);
		if(!$sFilePath || !is_readable($sFilePath))
			throw new IPCO_Exception(IPCO_Exception::TEMPLATETOFILE_INVALID_FILE);
		return $this->createParserFromFile($sFilePath);
	}
	
	public function createParserFromFile($sFilePath) {
		if(!file_exists($sFilePath) || !is_readable($sFilePath))
			throw new IPCO_Exception(IPCO_Exception::INVALID_FILE);
		$sContent = file_get_contents($sFilePath);
		return $this->createParserFromContent($sFilePath, $sContent, $this);
	}
	
	public function getContentParser() {
		return $this->m_oContentParser;
	}
	
	public function setContentParser(IPCO_IContentParser $oContentParser = null) {
		$this->m_oContentParser = $oContentParser;
	}
	
	public function getCallbacks() {
		return $this->m_oCallbacks;
	}
	
	public function setCallbacks(IPCO_ICallbacks $oCallbacks = null) {
		$this->m_oCallbacks = $oCallbacks;
	}
	
	/**
	 * Generate a valid classname for the given identifier.
	 * 
	 * @param string $sIdentifier
	 * 
	 * @return string
	 */
	public function getTemplateClassName($sIdentifier) {
		$sIdentifier = Encoding::toLower($sIdentifier);
		return 'IPCO_Compiled_' . Encoding::regReplace('[-/\\\\.: ]', '_', $sIdentifier);
	}
}

?>