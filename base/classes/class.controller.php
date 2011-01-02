<?php

abstract class Controller extends Cacheable {
	
	public abstract function render();
	
	public function process(Model $oModel, View $oView) {
		
	}
	
	public function operationalMessage($sMessage) {
		echo '<div style="text-align:center; border:1px solid #000; padding:5px; font-weight:bold;">'.$sMessage.'</div>';
	}
}

?>