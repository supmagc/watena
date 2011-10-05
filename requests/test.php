<?php

function error_handler($errno, $errstr, $errfile, $errline) {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    return true;
}
set_error_handler('error_handler');
try {
	trigger_error('MESSAGE', E_USER_WARNING);
}
catch(Exception $e) {echo $e;}
//trigger_error('MESSAGE', E_USER_WARNING);
//throw new Exception('EX', 0, null);
echo 'is this visible ?';
?>