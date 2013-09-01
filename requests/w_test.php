<?php
session_start();
ob_start();

class Test {
	
	protected function assertEquals($mActual, $mExpected, $sDescription) {
		
	}
}


define('TEST_DIR', realpath(dirname(__FILE__) . '/../test/'));
function test_scandir($sBase = TEST_DIR) {
	$aReturn = array();
	$aFiles = scandir($sBase);
	foreach($aFiles as $sFile) {
		$sPathLong = realpath($sBase . '/' . $sFile);
		$sPathShort = substr($sPathLong, strlen(TEST_DIR) + 1);
		if(substr($sFile, 0, 1) == '.') {
			continue;
		}
		else if(is_file($sPathLong)) {
			$aReturn []= $sPathShort;
		}
		else if(is_dir($sPathLong)) {
			$aReturn = array_merge($aReturn, test_scandir($sPathLong));
		}
	}
	return $aReturn;
}

if(!isset($_GET['test'])) {
	header('Content-Type: text/html; charset=utf-8;');
	$aPrevious = array();
	$aTests = test_scandir();
	$_SESSION['tests'] = $aTests;
	echo <<<EOT
<html>
<head>
<title>Watena testing</title>
<script>
function getXmlHttpRequest() {
	var xmlhttp;
	if(window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
		return new XMLHttpRequest();
	}
	else {// code for IE6, IE5
		return new ActiveXObject("Microsoft.XMLHTTP");
	}	
}

function send(sTest) {
	console.log("Requesting test: " + sTest);
	var oRequest = getXmlHttpRequest();
	oRequest.onreadystatechange = function() {
		if(oRequest.readyState == 4 && oRequest.status == 200) {
			console.log("Test result: " + oRequest.responseText);
		}
	};		
	oRequest.open('GET', 'w_test.php?test='+sTest, true);
	oRequest.send();
}
</script>
<style>
html, body {
	width: 100%;
}
			
.rowA {
	background: #DDD;
}
.rowB {
	background: #AAA;
}
</style>
</head>
<body>
<table width="100%" border="1">
EOT;
	foreach($aTests as $nIndex => $sTest) {
		$sTestSave = $sTest; // urlencode($sTest);
		echo <<<EOT
		<tr><td>[<a href="javascript:send('$nIndex');">test</a>] $sTest</td></tr>
EOT;
		$aPrevious []= $sTest;
	}
	echo <<<EOT
</table>
			<div>Test A</div>
			<div>Test B</div>
			<div>Test C</div>
			<div>Test D</div>
</body>
</html>
EOT;
}
else if(is_numeric($_GET['test']) && !empty($_SESSION['tests'])) {
	header('Content-Type: text/plain; charset=utf-8;');
	echo $_GET['test'];
}
?>
