<?php

class EchoLog extends Plugin implements ILogProcessor {
	
	public function process($sIdentifier, $nLevel, $sFile, $nLine, $sMessage, array $aData = array(), array $aTrace = array()) {
		$sLevel = ucfirst(Logger::getLevelName($nLevel));
		echo <<<EOT
			<fieldset style="font: 14px arial; letter-spacing: 1; margin: 10px; color: #000; background: #FFF;">
				<legend style="font-variant: small-caps;">Logger: $sLevel ($sIdentifier)</legend>
			<table cellspacing="0" cellpadding="0" style="font-size: 10px; color: #666;">
				<tr>
					<td valign="top">Message: </td>
					<td><strong>$sMessage</strong></td>
				</tr>
				<tr>
					<td valign="top">Origin: </td>
					<td><strong>$sFile</strong> (<i>line: $nLine</i>)</td>
				</tr>
				<tr>
					<td valign="top">Data: </td>
					<td><strong>Show/Hide</strong></td>
				</tr>
				<tr>
					<td valign="top">Trace: </td>
					<td><strong>Show/Hide</strong>{$this->getTrace($aTrace)}</td>
				</tr>
			</table>
			</fieldset>
EOT;
	}
	
	public function getTrace(array $aTrace = array()) {
		$sReturn = '<ul>';
		foreach($aTrace as $aPart) {
			$sReturn .= '<li>';
			$sReturn .= isset($aPart['class']) ? ($aPart['class'] . $aPart['type']) : '';
			if(isset($aPart['function'])) {
				$sReturn .= $aPart['function'];
				$sReturn .= '(';
				foreach($aPart['args'] as $sName => $sValue) {
					if(is_object($sValue)) $s = get_class($sValue) . '(' . $sValue->__toString() . ')';
					else if(is_array($sValue)) $s = 'Array';
					else if(is_bool($sValue)) $s = $sValue ? "true" : "false";
					else if(is_null($sValue)) $s = 'null';
					else $s = '' . $sValue;
					if(Encoding::length($s) > 128) $s = Encoding::substring($s, 0, 125) . '...';
					$sReturn .= $s;
				}
				$sReturn .= ')';
			}
			$sReturn .= " - $aPart[file] (line: $aPart[line])</li>";
		}
		return $sReturn . '</ul>';
	}
	
	/**
	* Retrieve version information of this plugin.
	* The format is an associative array as follows:
	* 'major' => Major version (int)
	* 'minor' => Minor version (int)
	* 'build' => Build version (int)
	* 'state' => Naming of the production state
	*/
	public function getVersion() {
		return array('major' => 0, 'minor' => 1, 'build' => 1, 'state' => 'dev');
	}
}

?>