<?php

require_once '../../base/classes/static.encoding.php';
Encoding::init('UTF-8');

require_once 'ipco.php';
require_once 'ipco_base.php';
require_once 'ipco_parser.php';
require_once 'ipco_compiled.php';
require_once 'ipco_expression.php';

echo new IPCO_Expression('-1 + (!(2*a)) != 2 & 8 - substr(\'bla\', 1, 2) & 3 && \'12\' > 3+8 AND !8 + 2 OR 3', new IPCO());

exit;

// TODO: First parse and search for function calls and component calls etc ... we need a syntax for this
// TODO: create parseParameters($sParams)


new IPCO_Condition('(\'a\' & !(\'b\' | 12.56 = 3.8))', new IPCO());
//$ipco = new IPCO();
//$ipco->load('source');
?>