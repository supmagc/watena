<?php
require_plugin('AdminModuleLoader');

class Admin extends Plugin {
	
	private $m_oLoader;
	private static $s_oSingleton;
	
	public function init() {
		self::$s_oSingleton = $this;
		$this->m_oLoader = parent::getWatena()->getContext()->getPlugin('AdminModuleLoader');
		
		Events::registerEventCallback('prepareHtmlModel', array($this, 'prepareHtmlModel'));
	}
	
	public function prepareHtmlModel(HtmlModel $oModel) {
		$oModel->addCssLink('theme/admin/css/admin.main.css');
		$oModel->addCssLink('theme/admin/css/admin.overlay.css');
		$oModel->addCssLink('theme/admin/jqueryui/jquery-ui-1.10.4.custom.min.css');
		$oModel->addJavascriptLink('theme/admin/js/jquery-1.10.2.min.js');
		$oModel->addJavascriptLink('theme/admin/jqueryui/jquery-ui-1.10.4.custom.min.js');
		$oModel->addJavascriptCode("alert('YES');");
		$oModel->addJavascriptLink('theme/admin/js/watena-admin.js');
	}
	
	/**
	 * Get the main loader for the admin modules.
	 * 
	 * @return AdminModuleLoader
	 */
	public static function getLoader() {
		return self::$s_oSingleton->m_oLoader;
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