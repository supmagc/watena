<?php namespace Watena\Core;

interface ILogProcessor {
	
	public function loggerProcess($sIdentifier, $nLevel, $sFile, $nLine, $sMessage, array $aData, array $aTrace);
}
