<?php

class TemplateBuilder {
	
	public function __construct() {
		
	}
	
	public function onContent($sData) {
		echo $sData;
	}
	
	public function onTagOpen($sName) {
		echo "<$sName";
	}
	
	public function onTagClose($bSingle = false) {
		echo $bSingle ? ' />' : '>';
	}
	
	public function onTagEnd($sName) {
		echo "</$sName>";
	}

	public function onAttributeName($sName) {
		echo " $sName=";
	}
	
	public function onAttributeValueDouble($sValue) {
		echo "\"$sValue\"";
	}
	
	public function onAttributeValueSingle($sValue) {
		echo "'$sValue'";
	}
	
	public function onXml($sData) {
		echo "<?xml$sData?>";
	}
	
	public function onPhp($sData) {
		echo "<?php$sData?>";
	}
	
	public function onComment($sData) {
		echo "<!--$sData-->";
	}
	
	public function onCData($sData) {
		echo "<![CDATA[$sData]]>";
	}
	
	public function onDoctype($sData) {
		echo "<!DOCTYPE$sData>";
	}
}

?>