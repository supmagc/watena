<?php

class HelloWorldView extends View {
	
	public function headers(Model $oModel = null) {
		header('X-Watena: Hello World !');
	}
	
	public function render(Model $oModel = null) {
		echo 'Hello World !';
	}
}
?>