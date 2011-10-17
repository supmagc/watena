<?php

interface ILogProcessor {
	
	public function loggingProces($sIdentifier, $nLevel, $sFile, $nLine, $sMessage, array $aData, array $aTrace);
}

?>