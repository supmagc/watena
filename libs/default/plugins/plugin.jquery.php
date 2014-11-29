<?php
/**
 * Plugin to manage multiple jquery request from multiple sources.
 * It verifies that nu double include will happen, and checks the
 * required version if needed.
 * 
 * @author Jelle
 * @version 0.1.0
 */
class JQuery extends Plugin {
	
	private static $s_sJQueryVersion = false;
	private static $s_sJQueryUiVersion = false;
	private static $s_sJQueryUiTheme = false;
	
	private static $s_oSingleton;

	/**
	 * @see Cacheable::init()
	 */
	public function init() {
		Events::registerEventCallback('prepareHtmlModel', array($this, 'onPrepareHtmlModel'));
		self::$s_oSingleton = $this;
	}
	
	/**
	 * Callback event which injects the correct links into the HtmlModel.
	 * 
	 * @param HtmlModel $oModel
	 */
	public function onPrepareHtmlModel(HtmlModel $oModel) {
		if(self::$s_sJQueryVersion !== false) {
			$sVersion = self::$s_sJQueryVersion ?: self::$s_oSingleton->getConfig('JQUERY_VERSION_DEFAULT', null);
			$oModel->addJavascriptLink(sprintf('//ajax.googleapis.com/ajax/libs/jquery/%s/jquery.min.js', $sVersion), true);
		}
		
		if(self::$s_sJQueryUiVersion !== false && self::$s_sJQueryUiTheme !== false) {
			$sVersion = self::$s_sJQueryUiVersion ?: self::$s_oSingleton->getConfig('JQUERYUI_VERSION_DEFAULT', null);
			$sTheme = self::$s_sJQueryUiTheme ?: self::$s_oSingleton->getConfig('JQUERYUI_THEME_DEFAULT', null);
			$oModel->addJavascriptLink(sprintf('//ajax.googleapis.com/ajax/libs/jqueryui/%s/jquery-ui.min.js', $sVersion), true);
			$oModel->addCssLink(sprintf('//ajax.googleapis.com/ajax/libs/jqueryui/%s/themes/%s/jquery-ui.css', $sVersion, Encoding::toLower($sTheme)), 'all', true);
		}
	}
	
	/**
	 * Flag the requirement of jquery.
	 * If no version is specified, the default from the config file will be used.
	 * 
	 * @param string $sVersion If you specify a version, it will be used, it you specify null, the default will be used, if you specify false, you disable jquery.
	 */
	public static function requireJQuery($sVersion = null) {
		if(self::$s_sJQueryVersion && $sVersion != self::$s_sJQueryVersion)
			Logger::getInstance('JQuery')->getLogger()->warning('JQuery requested multiple times with different versions {version1} <> {version2}', array('version1' => self::$s_sJQueryVersion, 'version2' => $sVersion));
		
		self::$s_sJQueryVersion = $sVersion;
	}
	
	/**
	 * Flag the requirement of jquery-ui.
	 * If no version/theme is specified, the defaults from the config file will be used.
	 * 
	 * @param string $sTheme If you specify a theme, it will be used, it you specify null, the default will be used, if you specify false, you disable jquery-ui.
	 * @param string $sVersion If you specify a version, it will be used, it you specify null, the default will be used, if you specify false, you disable jquery-ui.
	 */
	public static function requireJQueryUI($sTheme = null, $sVersion = null) {
		if(self::$s_sJQueryUiVersion && $sVersion != self::$s_sJQueryUiVersion)
			Logger::getInstance('JQuery')->warning('JQueryUi requested multiple times with different versions {version1} <> {version2}', array('version1' => self::$s_sJQueryUiVersion, 'version2' => $sVersion));
		if(self::$s_sJQueryUiTheme && $sTheme != self::$s_sJQueryUiTheme)
			Logger::getInstance('JQuery')->warning('JQueryUi requested multiple times with different themes {theme1} <> {theme2}', array('theme1' => self::$s_sJQueryUiTheme, 'theme2' => $sTheme));
			
		self::$s_sJQueryUiVersion = $sVersion;
		self::$s_sJQueryUiTheme = $sTheme;
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
			'build' => 0,
			'state' => 'dev'
		);
	}
}