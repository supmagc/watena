<?php

class TemplateBuilder {
	
	private $m_aBuffer = array();
	private $m_sLastTagName;
	private $m_sLastAttributeName;
	
	public function __construct() {
		
	}
	
	public function __toString() {
		return implode('', $this->m_aBuffer);
	}
	
	public function onContent($sData) {
		$this->m_aBuffer []=   $sData;
	}
	
	public function onTagOpen($sName) {
		$sName = trim($sName);
		$this->m_sLastTagName = $sName;
		$this->m_aBuffer []= "<$sName";
	}
	
	public function onTagClose() {
		$this->m_aBuffer []=  '>';
	}
	
	public function onTagSingleClose() {
		$this->m_aBuffer []=  ' />';
	}
	
	public function onTagEnd($sName) {
		$sName = trim($sName);
		$this->m_aBuffer []=  "</$sName>";
	}

	public function onAttributeName($sName) {
		$sName = trim($sName);
		$this->m_aBuffer []=  " $sName=";
	}
	
	public function onAttributeValueDouble($sValue) {
		$this->m_aBuffer []=  "\"$sValue\"";
	}
	
	public function onAttributeValueSingle($sValue) {
		$this->m_aBuffer []=  "'$sValue'";
	}
	
	public function onXml($sData) {
		$this->m_aBuffer []=  "<?xml$sData?>";
	}
	
	public function onPhp($sData) {
		$this->m_aBuffer []=  "<?php$sData?>";
	}
	
	public function onComment($sData) {
		$this->m_aBuffer []=  "<!--$sData-->";
	}
	
	public function onCData($sData) {
		$this->m_aBuffer []=  "<![CDATA[$sData]]>";
	}
	
	public function onDoctype($sData) {
		$this->m_aBuffer []=  "<!DOCTYPE$sData>";
	}
}

?>