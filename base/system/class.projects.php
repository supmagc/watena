<?php

class _ProjectGroup {
	
}

class Projects extends Object {
	
	public function __construct() {
		$aProjects = explode(',', parent::getWatena()->getConfig('PROJECTS', ''));
		foreach($aProjects as $sProject) {
			$sProject = Encoding::trim($sProject);
			$sPath = parent::getWatena()->getPath($sProject);
		}
	}
}
?>
