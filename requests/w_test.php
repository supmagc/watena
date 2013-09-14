<?php
define('TEST_DIR', realpath(dirname(__FILE__) . '/../test/'));
session_start();
ob_start();

class Test {
	
	private $m_aErrors = array();
	private $m_sMethodName;
	private $m_sTestName;
	
	public function setup() {}
	public function teardown() {}
	
	public final function run() {
		$this->setup();
		
		$oClass = new ReflectionClass($this);
		$aMethods = $oClass->getMethods(ReflectionMethod::IS_PUBLIC);
		foreach($aMethods as $oMethod) {
			$aMatches = array();
			if(preg_match('/test([a-z0-9_]+)/i', $oMethod->name, $aMatches)) {
				$this->m_sMethodName = $oMethod->name;
				$this->m_sTestName = $aMatches[1];
				$oMethod->invoke($this);
			}
		}
		
		$this->teardown();
		
		if(count($this->m_aErrors) == 0) {
			echo '--ok--';
		}
		else {
			echo "--no--\r\n" . implode("\r\n", $this->m_aErrors);
		}
	}
	
	protected function assertEquals($mExpected, $mActual, $sDescription = null) {
		return $mActual === $mExpected || $this->error('EQUALS', "$mActual === $mExpected", $sDescription);
	}
	
	protected function assertNotEquals($mExpected, $mActual, $sDescription = null) {
		return $mActual !== $mExpected || $this->error('NOTEQUALS', "$mActual !== $mExpected", $sDescription);
	}
	
	protected function assertTrue($mActual, $sDescription = null) {
		return $mActual == true || $this->error('TRUE', "$mActual", $sDescription);
	}
	
	protected function assertFalse($mActual, $sDescription = null) {
		return $mActual == false || $this->error('FALSE', "$mActual", $sDescription);
	}
	
	private function error($sAssert, $sCondition, $sDescription) {
		$aTrace = debug_backtrace(false);
		$nLine = $aTrace[1]['line'];
		$this->m_aErrors []= sprintf('<div class="assert"><font class="line">%d</font>@<font class="name">%s</font> :: %s(<font class="condition">%s</font>) <font class="description">%s</font></div>', $nLine, $this->m_sTestName, strtoupper($sAssert), $sCondition, $sDescription);
		return false;
	}
}

function test_scandir($sBase = TEST_DIR) {
	$aReturn = array();
	$aFiles = scandir($sBase);
	foreach($aFiles as $sFile) {
		$sPathLong = realpath($sBase . '/' . $sFile);
		$sPathShort = substr($sPathLong, strlen(TEST_DIR) + 1);
		if(substr($sFile, 0, 1) == '.' || !preg_match('/[0-9]+_[a-z0-9_]+(\.php)?/i', $sFile)) {
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

function getTestName($sPath) {
	$aMatches = array();
	if(preg_match('/[\/\\][0-9]+_([a-z0-9_]+)(\.php)?$/i', $sPath, $aMatches)) {
		return implode('', array_map('ucfirst', explode('_', strtolower($aMatches[1])))) . 'Test';
	}
	else {
		return false;
	}
}

function generateTests() {
	$aTests = test_scandir();
	$_SESSION['tests'] = $aTests;
	foreach($aTests as $nIndex => $sTest) {
		$sClass = $nIndex % 2 == 0 ? 'rowEven' : 'rowOdd';
		$sField = "Test_$nIndex";
		$sName = getTestName($sTest);
		echo "<div id=\"$sField\" class=\"row $sClass\">
			<div id=\"{$sField}_title\" class=\"title\">Waiting for test to be run ...</div>
			<div id=\"{$sField}_head\" class=\"head\">[<a href=\"javascript:send('$nIndex', '$sField');\">test</a>] <strong>$sName</strong> (<i>$sTest</i>)</div>
			<div id=\"{$sField}_content\" class=\"content\" styme=\"display:none;\"></div>
		</div>";
		$aPrevious []= $sTest;
	}
}

function generateTestsAll() {
	$aTests = test_scandir();
	$_SESSION['tests'] = $aTests;
	foreach($aTests as $nIndex => $sTest) {
		$nTimeout = $nIndex * 1000 + 1;
		echo "\tsetTimeout('send(\\'$nIndex\\', \\'Test_$nIndex\\');', $nTimeout);\r\n";
	}
}

function runTest($nTest) {
	foreach($_SESSION['tests'] as $nIndex => $sTest) {
		$sPath = TEST_DIR . DIRECTORY_SEPARATOR . $sTest;
		if(is_readable($sPath)) {
			include_once $sPath;
			if($nIndex == $nTest) {
				$sClass = getTestName($sTest);
				if($sClass) {
					if(class_exists($sClass) && in_array('Test', class_parents($sClass))) {
						$oObj = new $sClass();
						$oObj->run();
					}
				}
				break;
			}
		}
	}
}

if(isset($_GET['test']) && is_numeric($_GET['test'])) {
	header('Content-Type: text/plain; charset=utf-8;');
	runTest($_GET['test']);
	exit;
}
else {
	header('Content-Type: text/html; charset=utf-8;');
}
?>
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

function send(sTest, sField) {
	setField(sField, "rowLoading", "Loading test ...");
	var oRequest = getXmlHttpRequest();
	oRequest.onreadystatechange = function() {
		if(oRequest.readyState == 4 && oRequest.status == 200) {
			var oRegex = new RegExp("^--(ok|no)--$");
			var aLines = oRequest.responseText.split("\r\n");
			if(aLines.length > 0 && oRegex.test(aLines[0])) {
				if(oRegex.exec(aLines[0])[1] == "ok") {
					setField(sField, "rowSuccess", "Test Succeeded !");
				}
				else {
					var sContent = "";
					for(i=1 ; i<aLines.length ; ++i) sContent += aLines[i];
					setField(sField, "rowError", "Test failed !", sContent);
				}
			}
			else {
				setField(sField, "rowError", "No test found !", oRequest.responseText);
			}
		}
	};		
	oRequest.open('GET', 'w_test.php?test='+sTest, true);
	oRequest.send();
}

function setField(sField, sClassName, sTitle, sContent) {
	document.getElementById(sField).className = "row " + sClassName;
	document.getElementById(sField+"_title").innerHTML = sTitle;
	document.getElementById(sField+"_content").innerHTML = sContent;
	document.getElementById(sField+"_content").style.display = sContent ? "block" : "none";
}

function all() {
<?php generateTestsAll(); ?>
}
</script>
<style>
html, body {
	width: 100%;
	padding: 0px;
	margin: 0px;
	color: #666px;
	font: 10px verdana;
}
h2 {
	background: #AAF;
	border-bottom: #666 1px solid;
	margin-bottom:0px;
	padding: 10px;
}
.assert {
	font: 15px arial;
}
.assert .line, .assert .name {
	color: #333;
	font-weight: 900;
}
.assert .condition {
	color: #900;
	font: 12px monospace italic;
	text-decoration: underline;
}
.assert .description {
	color: #666;
	font: 10px arial;
}
.rowOdd {
	background: #DDD;
}
.rowEven {
	background: #AAA;
}
.rowLoading {
	background: #FFA;
}
.rowSuccess {
	background: #AFA;
}
.rowError {
	background: #FAA;
}
.row {
	margin: 0px 5px;
	padding: 5px;
	border-bottom: #666 1px solid;
}
.row .head {
}
.row .title {
	float:right;
	font-weight: bold;
}
.row .content {
	clear: both;
}
</style>
</head>
<body>
<h2>Watena testing [<a href="javascript:all();">Run All Tests</a>]</h2>
<?php generateTests(); ?>
</body>
</html>