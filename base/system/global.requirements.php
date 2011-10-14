<?php

function require_initialise(RequirementBuffer $oBuffer) {
	$GLOBALS['REQUIREMENTBUFFERINSTANCE'] = $oBuffer;
}

function require_clear() {
	unset($GLOBALS['REQUIREMENTBUFFERINSTANCE']);
}

function require_file($mFiles) {
	RequirementBuffer::getInstance();
}


function require_model($mFiles) {

}


function require_view($mFiles) {

}


function require_controller($mFiles) {

}

?>