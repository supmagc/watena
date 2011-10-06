<?php

class EchoLog extends Plugin implements ILogProcessor {
	
	public function process($sIdentifier, $nLevel, $sFile, $nLine, $sMessage, array $aData = array(), array $aTrace = array()) {
		$sLevel = ucfirst(Logger::getLevelName($nLevel));
		echo <<<EOT
			<fieldset style="font: 14px arial; letter-spacing: 1; margin: 10px; color: #000; background: #FFF;">
				<legend style="font-variant: small-caps;">Logger: $sLevel ($sIdentifier)</legend>
			<table cellspacing="0" cellpadding="0" style="font-size: 10px; color: #666;">
				<tr>
					<td>Message: </td>
					<td><strong>$sMessage</strong></td>
				</tr>
				<tr>
					<td>Origin: </td>
					<td><strong>$sFile</strong> (<i>line: $nLine</i>)</td>
				</tr>
				<tr>
					<td>Data: </td>
					<td><strong>Show/Hide</strong></td>
				</tr>
				<tr>
					<td>Trace: </td>
					<td><strong>Show/Hide</strong></td>
				</tr>
			</table>
			</fieldset>
EOT;
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