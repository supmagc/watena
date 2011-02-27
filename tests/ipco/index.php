<?php

require_once '../../base/classes/static.encoding.php';
Encoding::init('UTF-8');

require_once 'ipco.php';
require_once 'ipco_base.php';
require_once 'ipco_parser.php';
require_once 'ipco_processor.php';
require_once 'ipco_expression.php';

/*
echo new IPCO_Expression('-1 + (!(-2*\'a\')) is not 2^7 & {12, 3, 9, 8+2} & 8 - 0 & 3 && \'1\\\'\\\'2\' > 3+8 AND !8 + 2 OR 3', new IPCO());
echo "\n<br />\n";
echo new IPCO_Expression('substr(1, 2)', new IPCO());
echo "\n<br />\n";
echo new IPCO_Expression('test.substr(1, pow(2, 5) + 5 * 9)[2, \'test\']', new IPCO());
exit;
*/

$ipco = new IPCO();
$ipco->load('source');
?>