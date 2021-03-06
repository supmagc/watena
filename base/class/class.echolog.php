<?php

class EchoLog implements ILogProcessor {
	
	private static $s_nFieldCount;
	
	public function loggerProcess($sIdentifier, $nLevel, $sFile, $nLine, $sMessage, array $aData, array $aTrace) {
		$sMessage = htmlentities(str_replace(array_map(create_function('$s', 'return "{".$s."}";'), array_keys($aData)), array_map('toStringSingleLine', array_values($aData)), $sMessage));
		$sLevel = ucfirst(Logger::getLevelName($nLevel));
		$nFieldID = ++self::$s_nFieldCount;
		$sData = $this->getOpenableBox('<pre>' . htmlentities(toStringIntended($aData)) . '</pre>');
		$sTrace = $this->getOpenableBox($this->getTrace($aTrace));
		echo <<<EOT
<fieldset style="font: 14px arial; letter-spacing: 1; margin: 10px; color: #000; background: #FFF;">
	<legend style="font-variant: small-caps;">Logger: $sLevel ($sIdentifier)</legend>
<table cellspacing="1" cellpadding="1" style="font-size: 10px; color: #666; width: 100%;">
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
			echo <<<EOT
	<tr>
		<td valign="top">Data: </td>
		<td>$sData</td>
	</tr>
EOT;
		}
		echo <<<EOT
	<tr>
		<td valign="top">Trace: </td>
		<td>$sTrace</td>
	</tr>
</table>
</fieldset>
EOT;
	}
	
	public function getOpenableBox($sData) {
		$sID = 'ECHOLOGPART_' . ++self::$s_nFieldCount;
		$sReturn  = '<div id="'.$sID.'_SHOW"><strong style="cursor:pointer;">';
		$sReturn .= '<a onclick="document.getElementById(\'';
		$sReturn .= $sID . '_SHOW';
		$sReturn .= '\').style.display=\'none\';document.getElementById(\'';
		$sReturn .= $sID . '_HIDE';
		$sReturn .= '\').style.display=\'block\';">Show (currently hidden)</a>';
		$sReturn .= '</strong></div>';
		$sReturn .= '<div id="'.$sID.'_HIDE" style="display:none; background: #EEE; border: 1px dotted #CCC; padding: 5px; margin: 0px;">';
		$sReturn .= '<div style="float:right;"><strong style="cursor:pointer;">';
		$sReturn .= '<a onclick="document.getElementById(\'';
		$sReturn .= $sID . '_SHOW';
		$sReturn .= '\').style.display=\'block\';document.getElementById(\'';
		$sReturn .= $sID . '_HIDE';
		$sReturn .= '\').style.display=\'none\';"><u>Hide</u></a>';
		$sReturn .= '</strong></div>';
		$sReturn .= '<div style:"float:left;">';
		$sReturn .= $sData;
		$sReturn .= '</div>';
		return $sReturn;
	}
	
	public function getTrace(array $aTrace = array()) {
		$sReturn = "<ul>\n";
		foreach($aTrace as $aPart) {
			$sReturn .= '<li>';
			$sReturn .= isset($aPart['class']) ? ($aPart['class'] . $aPart['type']) : '';
			if(isset($aPart['function'])) {
				$sReturn .= $aPart['function'];
				$sReturn .= '(';
				if(isset($aPart['args'])) {
					$bFirst = true;
					foreach($aPart['args'] as $sName => $sValue) {
						if($bFirst) $bFirst = false;
						else $sReturn .= ', ';
						if(is_object($sValue)) $s = get_class($sValue) . '(' . (method_exists($sValue, '__toString') ? $sValue->__toString() : get_class($sValue)) . ')';
						else if(is_array($sValue)) $s = 'Array';
						else if(is_bool($sValue)) $s = $sValue ? "true" : "false";
						else if(is_null($sValue)) $s = 'null';
						else if(is_string($sValue)) $s = var_export($sValue, true);
						else $s = '' . $sValue;
						if(Encoding::length($s) > 128) $s = Encoding::substring($s, 0, 125) . '...';
						$sReturn .= $s;
					}
				}
				$sReturn .= ')';
			}
			if(isset($aPart['file']) && isset($aPart['line']))
				$sReturn .= " - $aPart[file] (line: $aPart[line])</li>\n";
		}
		$sReturn .= '<li>' . $this->getInitCall() . "</li>";
		return $sReturn . '</ul>';
	}
	
	public function getInitCall() {
		$sReturn = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
		if(isset($_SERVER['HTTP_HOST'])) $sReturn .= $_SERVER['HTTP_HOST'];
		if(isset($_SERVER['SERVER_PORT'])) $sReturn .= ':' . $_SERVER['SERVER_PORT'];
		if(isset($_SERVER['REQUEST_URI'])) $sReturn .= $_SERVER['REQUEST_URI'];
		if(isset($_SERVER['REQUEST_METHOD'])) $sReturn .= ' [' . $_SERVER['REQUEST_METHOD'] . ']';
		return $sReturn;
	}
}
