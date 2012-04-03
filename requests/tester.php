<?php
session_start();

header('Content-Type: text/html;charset=UTF-8');

print_r($_GET);
print_r($_POST);
print_r($_SESSION);
print_r($_SERVER);

?>