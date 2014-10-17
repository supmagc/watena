<?php
require_includeonce(dirname(__FILE__) . '/../ajax/ajax.php');
require_model('HtmlModel');

class Ajax extends Plugin {
	
	public static function prepareHtmlModel(HtmlModel $oModel) {
		$oModel->addJavascriptLink(self::getJsLink());
	}
	
	public static function getJsLink() {
		
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
		return array(
			'major' => 0,
			'minor' => 1,
			'build' => 1,
			'state' => 'dev'
		);
	}
}

?>