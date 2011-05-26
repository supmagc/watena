<?php

class _ProjectGroup {
	
	public static function _load($sPath) {
		
	}
}

class Projects extends Object {
	
	public function __construct() {
		$aProjects = explode(',', parent::getWatena()->getConfig('PROJECTS', ''));
		foreach($aProjects as $sProject) {
			$sProject = Encoding::trim($sProject);
			$sPath = parent::getWatena()->getPath($sProject);
			
			parent::getWatena()->getCache()->retrieve("W_PROJECTGROUP_$sProject", array('_ProjectGroup', '_load'), 10 + rand(0, 20), array($sPath));
		}
	}
}
?>
