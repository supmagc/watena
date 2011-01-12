<?php

class Template {
	
	public function __construct($sFile) {
		$oReader = new TemplateReader(file_get_contents($sFile));
		$oParser = new TemplateParser();
		print_r($oParser->parse($oReader));
	}
	
	private function _parse() {
		
	}
}

?>