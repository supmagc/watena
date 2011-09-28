<?php
function error_handler($errno, $errstr, $errfile, $errline) {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}
set_error_handler('error_handler');
trigger_error('MESSAGE', E_USER_WARNING);
//trigger_error('MESSAGE', E_USER_WARNING);
//throw new Exception('EX', 0, null);
echo 'is this visible ?';
?>