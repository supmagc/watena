<?php

// Inline PHP compilation
class IPCO {
	
	private $m_sSourceDirectory = '';
	private $m_sSourceExtension = '';
	private $m_sSourceSuffix = '';
	private $m_sCompiledDirectory = '';
	private $m_sCompiledPrefix = '';
	private $m_sCompiledSuffix = '';
	
	public function __construct($sSourceDirectory, $sSourceExtension) {
		$this->m_sSourceDirectory = $sSourceDirectory;
		$this->m_sSourceExtension = $sSourceExtension;
	}

	public function getParser($sIdentifier) {
		
	}
	
	public function getSourcePath($sIdentifier) {
		$sIdentifier = Encoding::toLower($sIdentifier);
		return $this->m_sSourceDirectory . '/' . $sIdentifier . '.' . $this->m_sSourceExtension;
	}
	
	public function getCompiledPath($sIdentifier) {
		$sIdentifier = Encoding::toLower($sIdentifier);
		return $this->m_sSourceDirectory . '/' . $this->m_sSourcePrefix . $sIdentifier . $this->m_sCompiledSuffix;
	}
	
	public function getClassName($sIdentifier) {
		$sIdentifier = Encoding::toLower($sIdentifier);
		return 'IPCO_Compiled_' . Encoding::regReplace('[-/\\. ]', '_', $sIdentifier);
	}
}

?>