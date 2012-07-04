<?php
define('NMVC', true);
include '../base/watena.php';

require_plugin('UserManager');

$oUser = User::load(27);
$oUser->setPassword('tester');
dump($oUser->verifyPassword('tester'));
?>