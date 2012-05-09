<?php

define('NMVC', true);
include '../base/watena.php';

$oMail = new Mail();
$oMail->to('supmagc@gmail.com');
$oMail->from('info@flandersisafestival.com');
$oMail->subject('This is a system-test-mail !!');
$oMail->body('content, content');
$oMail->send();

echo '<pre>';
echo $oMail->get();
echo '</pre>';

?>