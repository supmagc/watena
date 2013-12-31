<?php
define('PATH_ADMIN', realpath(dirname(__FILE__) . '/../'));

require_once PATH_ADMIN . '/system/class.adminmenu.php';
require_once PATH_ADMIN . '/system/class.admintab.php';
require_once PATH_ADMIN . '/system/class.admincontent.php';

require_plugin('AdminLoader');

class Admin extends Plugin {
	
	private $m_oLoader;
	private static $s_oSingleton;
	
	public function init() {
		self::$s_oSingleton = $this;
		$this->m_oLoader = parent::getWatena()->getContext()->getPlugin('AdminLoader');
	}
	
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