<?php

// Inline PHP compilation
class IPCO {
	
	private $m_sSourceDirectory = '';
	private $m_sSourcePrefix = '';
	private $m_sSourceSuffix = '';
	private $m_sCompiledDirectory = '';
	private $m_sCompiledPrefix = '';
	private $m_sCompiledSuffix = '';
	
	public function __construct($sSourceDirectory, $sSourcePrefix, $sSourceSuffix, $sCompiledDirectory, $sCompiledPrefix, $sCompiledSuffix) {
		$this->m_sSourceDirectory = $sSourceDirectory;
		$this->m_sSourcePrefix = $sSourcePrefix;
		$this->m_sSourceSuffix = $sSourceSuffix;
		$this->m_sCompiledDirectory = $sCompiledDirectory;
		$this->m_sCompiledPrefix = $sCompiledPrefix;
		$this->m_sCompiledSuffix = $sCompiledSuffix;
	}

	public function getParser($sIdentifier) {
		
	}
	
	public function getSourcePath($sIdentifier) {
		$sIdentifier = Encoding::stringToLower($sIdentifier);
		return $this->m_sSourceDirectory . '/' . $this->m_sSourcePrefix . $sIdentifier . $this->m_sCompiledSuffix;
	}
	
	public function getCompiledPath($sIdentifier) {
		$sIdentifier = Encoding::stringToLower($sIdentifier);
		return $this->m_sSourceDirectory . '/' . $this->m_sSourcePrefix . $sIdentifier . $this->m_sCompiledSuffix;
	}
	
	public function getClassName($sIdentifier) {
		$sIdentifier = Encoding::stringToLower($sIdentifier);
		return $this->m_sSourcePrefix . Encoding::regReplace('[-/\\. ]', '_', $sIdentifier);
	}
}

?>