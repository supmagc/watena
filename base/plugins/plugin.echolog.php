<?php

class EchoLog extends Plugin implements ILogProcessor {
	
	private static $s_nFieldCount;
	
	public function process($sIdentifier, $nLevel, $sFile, $nLine, $sMessage, array $aData = array(), array $aTrace = array()) {
		$sMessage = htmlentities(str_replace(array_map(create_function('$a', 'return \'{\'.$a.\'}\';'), array_keys($aData)), array_values($aData), $sMessage));
		$sLevel = ucfirst(Logger::getLevelName($nLevel));
		$nFieldID = ++self::$s_nFieldCount;
		echo <<<EOT
<fieldset style="font: 14px arial; letter-spacing: 1; margin: 10px; color: #000; background: #FFF;">
	<legend style="font-variant: small-caps;">Logger: $sLevel ($sIdentifier)</legend>
<table cellspacing="0" cellpadding="0" style="font-size: 10px; color: #666; width: 100%;">
	<tr>
		<td valign="top" width="75">Message: </td>
		<td><strong>$sMessage</strong></td>
	</tr>
	<tr>
		<td valign="top">Origin: </td>
		<td><strong>$sFile</strong> (<i>line: $nLine</i>)</td>
	</tr>
EOT;
		if(count($aData) > 0) {
			$sData = '<pre>' . htmlentities(var_export($aData, true)) . '</pre>';
			echo <<<EOT
	<tr>
		<td valign="top">Data: </td>
		<td>
			<div id="DATA_SHOW_$nFieldID"><strong>
				<a onclick="document.getElementById('DATA_SHOW_$nFieldID').style.display='none';document.getElementById('DATA_HIDE_$nFieldID').style.display='block';">Show</a>
			</strong></div>
			<div id="DATA_HIDE_$nFieldID" style="display:none; background: #EEE; border: 1px dotted #CCC; padding: 5px; margin: 5px;"><strong>
				<a onclick="document.getElementById('DATA_SHOW_$nFieldID').style.display='block';document.getElementById('DATA_HIDE_$nFieldID').style.display='none';"><u>Hide</u></a>
			</strong>
			$sData</div>
		</td>
	</tr>
EOT;
		}
		echo <<<EOT
	<tr>
		<td valign="top">Trace: </td>
		<td>
			<div id="TRACE_SHOW_$nFieldID"><strong>
				<a onclick="document.getElementById('TRACE_SHOW_$nFieldID').style.display='none';document.getElementById('TRACE_HIDE_$nFieldID').style.display='block';">Show</a>
			</strong></div>
			<div id="TRACE_HIDE_$nFieldID" style="display:none; background: #EEE; border: 1px dotted #CCC; padding: 5px; margin: 5px;">
				<div style="float:right;"><strong>
					<a onclick="document.getElementById('TRACE_SHOW_$nFieldID').style.display='block';document.getElementById('TRACE_HIDE_$nFieldID').style.display='none';"><u>Hide</u></a>
				</strong></div>
				<div style:"float:left;">{$this->getTrace($aTrace)}</div>
			</div>
		</td>
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
					else if(is_string($sValue)) $s = var_export($sValue, true);
					else $s = '' . $sValue;
					if(Encoding::length($s) > 128) $s = Encoding::substring($s, 0, 125) . '...';
					$sReturn .= $s;
				}
				$sReturn .= ')';
			}
			$sReturn .= " - $aPart[file] (line: $aPart[line])</li>";
		}
		// TODO: http or https ?
		// TODO: make this easier by adding a static getCurrent to Mapping
		$sReturn .= '<li>http://' . $_SERVER['HTTP_HOST'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'] . ' [' . $_SERVER['REQUEST_METHOD'] . ']</li>';
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