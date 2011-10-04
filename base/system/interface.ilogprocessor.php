<?php

interface ILogProcessor {
	
	public function process($sIdentifier, $nLevel, $sFile, $nLine, $sMessage, array $aData = array(), array $aTrace = array());
}

?>