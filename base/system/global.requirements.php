<?php

function require_initialise(RequirementBuffer $oBuffer) {
	$GLOBALS['REQUIREMENTBUFFERINSTANCE'] = $oBuffer;
}

function require_clear() {
	unset($GLOBALS['REQUIREMENTBUFFERINSTANCE']);
}

function require_include($mFile) {
	if(is_array($mFiles)) array_walk($mFile, $funcname)
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