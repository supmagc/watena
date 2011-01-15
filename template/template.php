<?php

class Template {
	
	public function __construct($sFile) {
		$oReader = new TemplateReader(file_get_contents($sFile));
		$oParser = new TemplateParser();
		echo $oParser->parse($oReader);
	}
}

?>