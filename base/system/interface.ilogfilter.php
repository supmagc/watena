<?php

interface ILogFilter {
	
	public function loggingFilter(&$sIdentifier, &$nLevel, &$sFile, &$nLine, &$sMessage, array &$aData, array &$aTrace);
}