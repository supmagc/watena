<?php

class ContextLogFilter extends Object implements ILogFilter {
	
	public function loggerFilter(&$sIdentifier, &$nLevel, $sFile, $nLine, $sMessage, array $aData, array $aTrace) {
		if($sIdentifier == require_logger()->getIdentifier()) {
			echo 'ggggggggggggggggggggggggggg';
			return false;
		}
	}
	
	public function startWatchdog() {
		
	}
	
	public function stopWatchdog() {
		
	}
}

?>