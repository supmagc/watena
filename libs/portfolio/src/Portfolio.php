<?php namespace Watena\Libs\Portfolio;

use Watena\Core\Requirements;

Requirements::pluginLoaded('\Watena\Libs\Base\JQuery');

define('PATH_PORTFOLIO', realpath(dirname(__FILE__) . '/../system/'));

class Portfolio extends Plugin {
	
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
