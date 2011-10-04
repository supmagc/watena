<?php

class EchoLog extends Plugin implements ILogProcessor {
	
	public function process($sIdentifier, $nLevel, $sFile, $nLine, $sMessage, array $aData = array(), array $aTrace = array()) {
		$args = func_get_args();
		echo '<pre>';
		print_r($args);
		echo '</pre>';
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
		return array('major' => 0, 'minor' => 1, 'build' => 1, 'state' => 'dev');
	}
}

?>