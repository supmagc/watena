<?php
define('PATH_ADMIN', realpath(dirname(__FILE__)));

require_includeonce(PATH_ADMIN . '/system/interface.iadmingeneratable.php');

require_includeonce(PATH_ADMIN . '/system/class.adminmodule.php');
require_includeonce(PATH_ADMIN . '/system/class.adminmoduletab.php');
require_includeonce(PATH_ADMIN . '/system/class.adminmoduleitem.php');
require_includeonce(PATH_ADMIN . '/system/class.adminmodulecontent.php');
require_includeonce(PATH_ADMIN . '/system/class.adminmodulecontentrequest.php');
require_includeonce(PATH_ADMIN . '/system/class.adminmodulecontentresponse.php');
require_includeonce(PATH_ADMIN . '/system/class.adminjsfunctions.php');
require_includeonce(PATH_ADMIN . '/system/class.adminplugin.php');

require_plugin('templateloader');
require_plugin('AdminModuleLoader');
require_plugin('JQuery');

class Admin extends Plugin {
	
	private $m_oLoader;
	private static $s_oSingleton;
	
	public function init() {
		self::$s_oSingleton = $this;
		$this->m_oLoader = parent::getWatena()->getContext()->getPlugin('AdminModuleLoader');
		
		Events::registerEventCallback('prepareHtmlModel', array($this, 'onPrepareHtmlModel'));
		
		JQuery::requireJQuery();
		JQuery::requireJQueryUI();
	}
	
	public function onPrepareHtmlModel(HtmlModel $oModel) {
		$oModel->addCssLink('theme/admin/css/admin.main.css');
		$oModel->addCssLink('theme/admin/css/admin.overlay.css');
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