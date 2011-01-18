<?php

require_once '../../base/classes/static.encoding.php';
Encoding::init('UTF-8');

require_once 'ipco.php';
require_once 'ipco_base.php';
require_once 'ipco_parser.php';
require_once 'ipco_compiled.php';
require_once 'ipco_condition.php';


new IPCO_Condition('(\'a\' & !(\'b\' | 12.56 = 3.8))', new IPCO());
//$ipco = new IPCO();
//$ipco->load('source');
?>