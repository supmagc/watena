<?php

interface ILogFilter {
	
	public function loggerFilter(&$sIdentifier, &$nLevel, $sFile, $nLine, $sMessage, array $aData, array $aTrace);
}
