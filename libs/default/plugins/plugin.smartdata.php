<?php
require_includeonce(dirname(__FILE__) . '/../smartdata/index.php');
require_model('HtmlModel')
require_plugin('JQuery');

class SmartData extends Plugin {
	
	public function init() {
		Events::registerEventCallback(HtmlModel::EVENT_PREPAREHTMLMODEL, array($this, 'onPrepareHtmlModel'));
		
		JQuery::requireJQuery();
		JQuery::requireJQueryUI();
	}
	
	public function onPrepareHtmlModel(HtmlModel $oModel) {
		$oModel->addJavascriptLink('/theme/default/js/smartdata.js');
		$oModel->addJavascriptCode(''); // Inject form specific stuff
	}
}
