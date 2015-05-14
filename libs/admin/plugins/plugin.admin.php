<?php
require_plugin('templateloader'); # Required since it includes a needed interface

define('PATH_ADMIN', realpath(dirname(__FILE__) . '/../system/'));

require_includeonce(PATH_ADMIN . '/interface.iadmingeneratable.php');

require_includeonce(PATH_ADMIN . '/class.adminmodule.php');
require_includeonce(PATH_ADMIN . '/class.adminmoduletab.php');
require_includeonce(PATH_ADMIN . '/class.adminmoduleitem.php');
require_includeonce(PATH_ADMIN . '/class.adminmodulecontent.php');
require_includeonce(PATH_ADMIN . '/class.adminmodulecontentrequest.php');
require_includeonce(PATH_ADMIN . '/class.adminmodulecontentresponse.php');
require_includeonce(PATH_ADMIN . '/class.adminjsfunctions.php');
require_includeonce(PATH_ADMIN . '/class.adminplugin.php');

require_plugin('AdminModuleLoader');

class Admin extends Plugin {
	
	private $m_oLoader;
	private static $s_oSingleton;
	
	public function init() {
		self::$s_oSingleton = $this;
		$this->m_oLoader = parent::getWatena()->getContext()->getPlugin('AdminModuleLoader');
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
