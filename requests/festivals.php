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
			$aFestival['date_start'] = $aParts[2] ? makeDate($aParts[2]) : null;
			$aFestival['date_stop'] = ($aParts[2] && $aParts[3]) ? makeDate($aParts[2], $aParts[3]) : null;
				
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
			if($oData->mailCount < 2) {
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