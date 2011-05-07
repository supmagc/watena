<?php

abstract class View extends CacheableData {
	
	abstract public function render(Model $oModel);
}

?>