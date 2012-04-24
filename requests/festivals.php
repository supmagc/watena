<?php
function getGenreId($sGenre) {
	$sGenre = mb_strtolower($sGenre);
	if($sGenre == 'blues') return 1;
	if($sGenre == 'dance') return 2;
	if($sGenre == 'divers') return 3;
	if($sGenre == 'folk') return 4;
	if($sGenre == 'jazz') return 5;
	if($sGenre == 'klassiek') return 6;
	if($sGenre == 'metal') return 7;
	if($sGenre == 'pop') return 8;
	if($sGenre == 'reggae') return 9;
	if($sGenre == 'rock') return 10;
	if($sGenre == 'theater') return 11;
	if($sGenre == 'wereld') return 12;
	return null;
}

function getLocationTypeId($sLocationType) {
	$sLocationType = mb_strtolower($sLocationType);
	if($sLocationType == 'stad') return 1;
	if($sLocationType == 'strand') return 2;
	if($sLocationType == 'bos') return 3;
	if($sLocationType == 'wei') return 4;
	if($sLocationType == 'park') return 5;
	if($sLocationType == 'zaal') return 6;
	return null;
}

function makeDate($sDate, $nDayOffset = 0) {
	return date('Y-m-d', strtotime(str_replace('/', '-', $sDate)) + ($nDayOffset * 24 * 60 * 60));
}

function PDOInsert($sTable, $aData, $bTransaction = true) {
	global $PDO;
	$sQuery = 'INSERT INTO `'.$sTable.'` ('.implode(', ', array_map(create_function('$a', 'return "`$a`";'), array_keys($aData))).') VALUES ('.implode(', ', array_map(create_function('$a', 'return ":$a";'), array_keys($aData))).')';
	if($bTransaction) $PDO->beginTransaction();
	try {
		$oStatement = $PDO->prepare($sQuery);
		$oStatement->execute($aData);
		$mID = $PDO->lastInsertId();
	}
	catch(PDOException $e) {
		if($bTransaction) $PDO->rollBack();
		throw $e;
	}
	if($bTransaction) $PDO->commit();
	return $mID;
}

mb_internal_encoding('UTF-8');
header('Content-Type: text/html;charset=UTF-8');
$PDO = new PDO('mysql:dbname=toevla;host=127.0.0.1;charset=UTF-8', 'toevla_admin', 'toevlaanderen');
$PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$PDO->prepare('TRUNCATE `festival`')->execute();

$sContent = file_get_contents('listing.csv');
$sContent = mb_convert_encoding($sContent, 'UTF-8');
$aLines = explode("\n", $sContent);
$aFestivals = array();

foreach($aLines as $sLine) {
	$aParts = explode(";", $sLine);
	$aFestival = array();
	foreach($aParts as $nIndex => $sPart) {
		$sPart = trim($sPart);
		if(mb_strlen($sPart) > 0 && mb_substr($sPart, 0, 1) === '"' && mb_substr($sPart, mb_strlen($sPart) - 1, 1) === '"')
			$sPart = mb_substr($sPart, 1, mb_strlen($sPart) - 2);
		$sPart = trim($sPart);
		if($nIndex == 0)  $aFestival['name'] = $sPart;
		if($nIndex == 1)  $aFestival['date_start'] = mb_strlen($sPart) > 8 ? makeDate($sPart) : null;
		if($nIndex == 2)  $aFestival['date_stop'] = mb_strlen($sPart) > 8 ? makeDate($sPart, 1) : null;
		if($nIndex == 3)  $aFestival['website'] = $sPart;
		if($nIndex == 4)  $aFestival['location'] = $sPart;
		if($nIndex == 5)  $aFestival['fmiv'] = $sPart ? 1 : 0;
		if($nIndex == 6)  $aFestival['fiaf'] = $sPart ? 1 : 0;
		if($nIndex == 8)  $aFestival['genreId'] = getGenreId($sPart);
		if($nIndex == 9)  $aFestival['locationTypeId'] = getLocationTypeId($sPart);
		if($nIndex == 10) $aFestival['visitors'] = is_numeric($sPart) ? (int)$sPart : 0;
		if($nIndex == 11) $aFestival['description_NL'] = $sPart;
		if($nIndex == 12) $aFestival['description_EN'] = $sPart;
		if($nIndex == 13) $aFestival['comments'] = $sPart;
	}
	$aFestival['hash'] = md5('TOEVLA-156+464' . $aParts[0]);
	$aFestival['picasa'] = 'user/105238180871871114280/albumid/5663284935360263649';
	$aFestival['flickr'] = 'set=307262&nsid=14846397@N00';
	$aFestival['data'] = 'Field@255$0$0;0$231$243;255$240$159;0$021;7$102;6$102;@Tree1$-0.05$0$-7.57$270$323.97$0$;Tree3$-3.9$0$-7.19$270$0$0$;Stand2$7.04$0$9.22$270$301.72$0$;Stand4$5.63$0$-8.72$270$62.99$0$;Stand6$1.08$0$10.47$270$278.97$0$;Speaker5$-6.99$0$2.9$270$212.49$0$;Speaker5$-7.02$0$-4.14$270$148.74$0$;Flag1$-1.8$0$-3.9$270$0$0$;Flag2$-11.03$0$-1.53$270$0$0$;Flag1$-4.1$0$-0.38$270$0$0$;Flag1$-2.16$0$3.27$270$0$0$;Flag2$-10.92$0$1.13$270$0$0$;Tree3$-8$0$-7.13$270$0$0$;Tree3$0.3$0$5.4$270$280.98$0$;Tree3$-3.59$0$5.37$270$344.73$0$;Tree3$-8.02$0$5.19$270$8.74$0$;Stand5$-3.96$0$-11.8$270$112.74$0$;Stand7$-3.18$0$10.39$270$86.24$0$;Stand6$11.24$0$-12.13$270$41.49$0$;Stand3$4.03$0$-13.94$270$75.49$0$;Stand1$-10.4$0$7.26$270$45.25$0$;Stand1$-11.6$0$-7.62$270$320.24$0$;Speaker4$2.8$0$3.05$270$336.48$0$;Speaker4$3.53$0$-6.2$270$35.99$0$;@';
	$aFestival['artists'] = '';
	$aFestival['quiz'] = '';
	$aFestivals []= $aFestival;
}

foreach($aFestivals as $aFestival) {
	PDOInsert('festival', $aFestival);
	echo '<pre>';
	print_r($aFestival);
	echo '</pre>';
}
?>