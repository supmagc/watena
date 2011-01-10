<?php

abstract class View extends Cacheable {
	
	abstract public function render(Model $oModel);
}

?>