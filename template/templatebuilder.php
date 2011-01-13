<?php

class TemplateBuilder {
	
	public function __construct() {
		
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
}

?>