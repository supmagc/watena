<?php

class AdminHelp extends AdminPlugin {
	
	public abstract function getVersion() {
		return array(
			'major' => 0,
			'minor' => 1,
			'build' => 0,
			'state' => 'dev'
		);
	}
	
}

?>