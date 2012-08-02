<?php
require_once('include.php');
session_start();


if(!isset($_SESSION['TMX_TEST'])) {
	$_SESSION['TMX_TEST'] = 0;
}
else {
	$_SESSION['TMX_TEST'] += 1;
}

function FirstTest($a, $b, $aValues) {
	$r = '0123456789ABCDEF';
	$sColor = '#'.substr($r, rand(0, 16), 1).substr($r, rand(0, 16), 1).substr($r, rand(0, 16), 1); 
	$obj = new TMX_Response();
	$obj->DOMChangeByID('test', $aValues['testValue'] . '<br />border-width will be ' . $a . '<br />Session-data: ' . $_SESSION['TMX_TEST'], false);
	$obj->ClassChangeByID('test', 'GreenBorder');
	$obj->StyleChangeByID('test', 'borderWidth', $a . 'px');
	$obj->StyleChange(TMX_Selector::Create()->Tag('form')->Tag('input'), "color", $sColor);
	$obj->StyleChange(TMX_Selector::Create()->Tag('body')->Childs("test", "test"), "color", $sColor);
	$obj->AddJSFile('A.js');
	$obj->AddCSSFile('B.css');
	
	//$obj->AddStyleChange('', 'borderWidth', '2px');
	$obj->CallFunction('alert', array($aValues['testValue']));
	return $obj;
}

if(isset($_GET['TMX'])) {
	$svr = new TMX_Server(true, 'TMX');
}
else {
	$clt = new TMX_Client('TMX.js', 'TMX');
	$req = new TMX_Request('http://localhost/Eclipse/AjaxManager/index.php?TMX=true', 'FirstTest', 'SendTestData', 2);
	$req->SetValue('testValue', 'hello \'world\' !');
	$clt->RegisterRequest($req);
	
	echo '<?xml version="1.0" encoding="ISO-8859-1" ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html><head><title>Ajax Test</title><style>div.SetBorder {border:#FF0000 solid 1px;}div.GreenBorder {border:#00FF00 solid 1px;}</style></head><body><div class="SetBorder" id="test">&nbsp;</div>';
	/*echo '<script>
function Test(bla) {
	alert(bla);
}
var tmp = window["alert"];
window["alert"].apply(this, ["hello"]);
</script>';*/
	$sTmp = $clt->Process(false);
	echo $sTmp;
	echo '<a href="javascript:document.getElementById(\'test\').innerHTML=\'...loading...\';SendTestData(document.testForm.size.value, false);">Link</a>';
	echo '<pre test="test">';
	echo htmlentities($sTmp);
	echo '</pre><form name="testForm"><input type="text" name="size" value="2" /></form><br />Session-data: '.$_SESSION['TMX_TEST'].'</body></html>';
}
?>
