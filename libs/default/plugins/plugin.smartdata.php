<?php
require_includeonce(dirname(__FILE__) . '/../smartdata/index.php');
require_plugin('JQuery');

class SmartData extends Plugin {
	
	public function init() {
		Events::registerEventCallback('prepareHtmlModel', array($this, 'onPrepareHtmlModel'));
		
		JQuery::requireJQuery();
		JQuery::requireJQueryUI();
	}
	
	public function onPrepareHtmlModel(HtmlModel $oModel) {
		$oModel->addJavascriptLink('/theme/default/js/smartdata.js');
		$oModel->addJavascriptCode(''); // Inject form specific stuff
	}
}
