<?php
define('NMVC', true);
include '../index.php';
set_time_limit(600);
ini_set('memory_limit', '256M');

require_plugin('DatabaseManager');
require_plugin('UserManager');


include 'export.php';
//$oConnection = DatabaseManager::getConnection('toevla');
$oConnection = new DbConnection('mysql:host=flandersisafestival.com;port=3306;dbname=toevla;charset=UTF-8;', 'remote', 'UNDERd0g');
//$oConnection = new DbConnection('mysql:host=online-3d-games.com;port=3306;dbname=toevla;charset=UTF-8;', 'remote', 'UNDERd0g');
UserManager::setDatabaseConnection($oConnection);
$sTokens = 'AAABuTmZApA3oBAMBYZCiwE1RXbfOspwZB9ZC5lREdWsJANw44yQDlf2ajruurwSHalStBzSde4wK3ZBEtZBuljXd170xUxJ9QNpEnKNHxZAxAZDZD';
$nTimeStart = Time() - 60*60*24*30*4.5;
$nTimeStop = Time();

$nCount = 0;

foreach($aData as $aRow) {

	if($nCount++ < 3600) continue;
	
	$aRow = json_decode($aRow['facebookData'], true);
	$nId = array_value($aRow, 'id');
	$sUserName = array_value($aRow, 'username');
	$sGender = array_value($aRow, 'gender');
	$sBirthday = array_value($aRow, 'birthday');
	$sFirstName = array_value($aRow, 'first_name');
	$sLastName = array_value($aRow, 'last_name');
	$sLocale = array_value($aRow, 'locale');
	$sTimezone = array_value($aRow, 'timezone');
	$sTimestamp = date('Y-m-d h:i:s', rand($nTimeStart, $nTimeStop));
	
	if($sUserName && $nId) {
		try {
			$oUser = User::create($sUserName);
			$oUser->setGender($sGender);
			$oUser->setBirthday($sBirthday);
			$oUser->setFirstname($sFirstName);
			$oUser->setLastname($sLastName);
			$oUser->setTimezone($sTimezone);
			$oUser->setLocale($sLocale);
			
			$oConnection->query('UPDATE `user` SET `timestamp` = :t WHERE `ID` = :i', array('t' => $sTimestamp, 'i' => $oUser->getId()));
			$oConnection->insert('user_connection', array(
				'userId' => $oUser->getId(),
				'provider' => 'ProviderFacebook',
				'connectionId' => $nId,
				'connectionData' => json_encode($aRow),
				'connectionTokens' => json_encode(str_shuffle($sTokens)),
				'timestamp' => $sTimestamp
			));
		}
		catch(Exception $e) {
			echo $e . "<br />\n";
		}
	}
}

parent::processMethod('addJavascriptLink', 
	array (
		0 => parent::processMethod(\'url\', array (0 => \'((((parent::processMember(\\\'"\\\', null) / parent::processMember(\\\'theme\\\', null)) / parent::processMember(\\\'admin\\\', null)) / parent::processMember(\\\'js\\\', null)) / parent::processMember(\\\'js"\\\', parent::processMember(\\\'overlib\\\', null)))\',), null),
	), 
	null
);
?>