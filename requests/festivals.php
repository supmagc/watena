<?php
define('NMVC', true);
include '../base/watena.php';

require_plugin('DatabaseManager');
require_plugin('ToeVla');

$oConnection = DatabaseManager::getConnection('toevladmin');

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

/*
$oConnection->query('TRUNCATE `festival`');
$sContent = file_get_contents('listing.csv');
$sContent = mb_convert_encoding($sContent, 'UTF-8');
$aLines = explode("\n", $sContent);
$aFestivals = array();

foreach($aLines as $sLine) {
	$aParts = explode(";", $sLine);
	$aParts = array_map(create_function('$a', 'return Encoding::trim($a, " \"\n\r");'), $aParts);
	$aFestival = array();
	if(count($aParts) == 14) {
		foreach($aParts as $nIndex => $sPart) {
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
		$aFestival['data'] = 'Field@255$0$0;0$231$243;255$240$159;0$021;7$102;6$102;@Tree1$-0.05$0$-7.57$270$323.97$0$;Tree3$-3.9$0$-7.19$270$0$0$;Stand2$7.04$0$9.22$270$301.72$0$;Stand4$5.63$0$-8.72$270$62.99$0$;Stand6$1.08$0$10.47$270$278.97$0$;Speaker5$-6.99$0$2.9$270$212.49$0$;Speaker5$-7.02$0$-4.14$270$148.74$0$;Flag1$-1.8$0$-3.9$270$0$0$;Flag2$-11.03$0$-1.53$270$0$0$;Flag1$-4.1$0$-0.38$270$0$0$;Flag1$-2.16$0$3.27$270$0$0$;Flag2$-10.92$0$1.13$270$0$0$;Tree3$-8$0$-7.13$270$0$0$;Tree3$0.3$0$5.4$270$280.98$0$;Tree3$-3.59$0$5.37$270$344.73$0$;Tree3$-8.02$0$5.19$270$8.74$0$;Stand5$-3.96$0$-11.8$270$112.74$0$;Stand7$-3.18$0$10.39$270$86.24$0$;Stand6$11.24$0$-12.13$270$41.49$0$;Stand3$4.03$0$-13.94$270$75.49$0$;Stand1$-10.4$0$7.26$270$45.25$0$;Stand1$-11.6$0$-7.62$270$320.24$0$;Speaker4$2.8$0$3.05$270$336.48$0$;Speaker4$3.53$0$-6.2$270$35.99$0$;@';
		$aFestival['artists'] = '';
		$aFestival['quiz'] = 'How many festivals are features in this game?§73§75§78;How many different styles of music do we feature?§12§10§8;How many stars are there in the pop Area§8§6§7;How many cups must you collect before you can recycle?§10§15§20;There\'s a statue of what in the city?§Your character§a Vinyl LP§a Bird;§§§;§§§;§§§;§§§;§§§;';
		$aFestivals []= $aFestival;
	}
	else {
		echo 'Invalid row!<br />';
	}
}

foreach($aFestivals as $aFestival) {
	$oConnection->insert('festival', $aFestival);
	echo '<pre>';
	print_r($aFestival);
	echo '</pre>';
}
*/

$sContent = file_get_contents('update.csv');
$sContent = mb_convert_encoding($sContent, 'UTF-8');
$aLines = explode("\n", $sContent);
$oConnection = DatabaseManager::getConnection('toevla');
$oTable = $oConnection->getTable('festival', 'name');

foreach($aLines as $sLine) {
	$aParts = explode(";", $sLine);
	$aParts = array_map(create_function('$a', 'return Encoding::trim($a, " \"\n\r");'), $aParts);
	$aFestival = array();

	if(count($aParts) == 17) {
		$oStatement = $oTable->select($aParts[1]);
		if($oStatement->rowCount() > 0) {
			$oData = $oStatement->fetchObject();
			$aFestival['location'] = $aParts[4];
			$aFestival['website'] = $aParts[5];
			$aFestival['adminName'] = $aParts[10];
			$aFestival['adminPhone'] = $aParts[16];
			$aFestival['adminMail'] = $aParts[11];
			$aFestival['date_start'] = makeDate($aParts[2]);
			$aFestival['date_stop'] = makeDate($aParts[2], $aParts[3]);
				
			$bError = false;
			$sData = ToeVla::parseFacebook($aParts[8], $bError);
			if(!$bError) $aFestival['facebook'] = $sData ?: $oData->facebook;
			else echo "<strong>ERROR:</strong><i>$sData</i><br />";
			
			$bError = false;
			$sData = ToeVla::parseTwitterName($aParts[9], $bError);
			if(!$bError) $aFestival['twitterName'] = $sData ?: $oData->twitterName;
			else echo "<strong>ERROR:</strong><i>$sData</i><br />";
				
			$bError = false;
			$sData = ToeVla::parseTwitterHash($aParts[12], $bError);
			if(!$bError) $aFestival['twitterHash'] = $sData ?: $oData->twitterHash;
			else echo "<strong>ERROR:</strong><i>$sData</i><br />";
				
			$bError = false;
			$sData = ToeVla::parsePicasa($aParts[13], $bError);
			if(!$bError) $aFestival['picasa'] = $sData ?: $oData->picasa;
			else echo "<strong>ERROR:</strong><i>$sData</i><br />";
				
			$bError = false;
			$sData = ToeVla::parseFlickr($aParts[14], $bError);
			if(!$bError) $aFestival['flickr'] = $sData ?: $oData->flickr;
			else echo "<strong>ERROR:</strong><i>$sData</i><br />";
				
			$bError = false;
			$sData = ToeVla::parseYoutube($aParts[15], $bError);
			if(!$bError) $aFestival['youtube'] = $sData ?: $oData->youtube;
			else echo "<strong>ERROR:</strong><i>$sData</i><br />";
				
			echo "Found: $aParts[1]<br />";
			if($oData->mailCount == 0) {
				echo '<pre>';
				$oTable->update($aFestival, $oData->name);
				print_r($aFestival);
				echo '</pre>';
			}
		}
		else {
			echo "Unknown festival: " . $aParts[1] . "<br />";
		}
	}
	else {
		echo "Invalid data <br />";
	}
}
?>