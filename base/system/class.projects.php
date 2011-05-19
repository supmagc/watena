<?php

class _ProjectGroup extends CacheableFile {
	
}

class Projects extends Object {
	
	public function __construct() {
		$aProjects = parent::getWatena()->getConfig('PROJECTS', '');
	}
}
?>
