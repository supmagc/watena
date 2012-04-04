<?php
define('NWATENA', true);
require_once('../base/watena.php');



$sKey = 'VfipE4kEsDwA6ljKWzKG5A';
$sSecret = 'AC97IOnvVdFdEAYxB7Y10DEiRrwI2tsUrdxJG2sA&';

$aParams = array(
	'oauth_nonce' => MD5(microtime(true)),
	'oauth_callback' => 'http://flandersisafestival.dev/tester.php',
	'oauth_signature_method' => 'HMAC-SHA1',
	'oauth_timestamp' => time(),
	'oauth_consumer_key' => $sKey,
	'oauth_version' => '1.0',
);

ksort($aParams);
$sParams = http_build_query($aParams, null, '&');

$sBase = 'POST&' . urlencode('https://api.twitter.com/oauth/request_token') . '&' . urlencode($sParams);
$sHash = base64_encode(hash_hmac('sha1', $sBase, $sSecret, true));
$aParams['oauth_signature'] = $sHash;

$oRequest = new WebRequest('https://api.twitter.com/oauth/request_token', 'POST');

$aOAuths = array();
foreach($aParams as $sKey => $sValue) {
	if(strpos($sKey, 'oauth_') === 0)
		$aOAuths []= urlencode($sKey) . '="' . urlencode($sValue) . '"';
	else 
		$oRequest->addField($sKey, $sValue);
}
$oRequest->addHeader('Authorization', 'OAuth ' . implode(', ', $aOAuths));


$sContent = $oRequest->send()->getContent();
$aContent = array();
parse_str($sContent, $aContent);
//print_r($aContent);

header('Location: https://api.twitter.com/oauth/authenticate?oauth_token=' . $aContent['oauth_token']);

?>