<?php
require_once '../../base/classes/static.encoding.php';
require_once '../../base/global.php';

Encoding::init('UTF-8');

require_once 'ipco.php';
require_once 'ipco_base.php';
require_once 'ipco_parser.php';
require_once 'ipco_processor.php';
require_once 'ipco_expression.php';
require_once 'ipco_componentwrapper.php';

class Tester {
	
	public $mPublic = true;
	private $mPrivate = true;
	public static $sPublic = true;
	private static $sPrivate = true;
}

$oTester = new Tester();
var_dump(property_exists($oTester, 'sPrivate'));
echo $oTester->sPrivate;

/*
echo new IPCO_Expression('-1 + (!(-2*\'a\')) is not 2^7 & {12, 3, 9, 8+2} & 8 - 0 & 3 && \'1\\\'\\\'2\' > 3+8 AND !8 + 2 OR 3', new IPCO());
echo "\n<br />\n";
echo new IPCO_Expression('substr(1, 2)', new IPCO());
echo "\n<br />\n";
echo new IPCO_Expression('test.substr(1, pow(2, 5) + 5 * 9)[2, \'test\']', new IPCO());
exit;
*/

$ipco = new IPCO();
echo $ipco->load('source');
?>