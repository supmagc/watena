<?php
session_start();

$aCheck = array(
	'class.cacheable.php',
	'class.cacheabledata.php',
	'class.cacheabledirectory.php',
	'class.cacheablefile.php',
	'class.cacheempty.php',
	'class.configurable.php',
	'class.context.php'
);

if(!isset($_SESSION['time'])) $_SESSION['time'] = 0.0;
if(!isset($_SESSION['page'])) $_SESSION['page'] = 1;

$nTime = microtime(true);

/*$aFiles = array();
if(!isset($_SESSION['files']) || $_SESSION['change'] < filemtime('../base/system')) {
	$hFiles = opendir('../base/system');
	while(($sFile = readdir($hFiles)) !== false) {
		$aFiles['../base/system/' . $sFile] = true; 
	}
	closedir($hFiles);
	$_SESSION['files'] = serialize($aFiles);
	$_SESSION['change'] = filemtime('../base/system');
}
else {
	$aFiles = unserialize($_SESSION['files']);
}*/

for($i=0 ; $i<3 ; ++$i) {
	//if(isset($aFiles[$aCheck[rand(0, 6)]])) {
	//}
	if(realpath($aCheck[rand(0, 6)])) {
	}
}

$nTime = microtime(true) - $nTime;

$_SESSION['time'] += $nTime;
++$_SESSION['page'];

echo 'Time:' . $nTime . "<br />\n";
echo 'Total:' . $_SESSION['time'] . "<br />\n";
echo 'Mean:' . ($_SESSION['time'] / $_SESSION['page']) . "<br />\n";

?>