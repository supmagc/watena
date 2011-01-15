<?php

require_once '../../base/classes/static.encoding.php';
Encoding::init('UTF-8');

require_once 'ipco.php';
require_once 'ipco_base.php';
require_once 'ipco_parser.php';

$ipco = new IPCO();
$ipco->load('source');
?>