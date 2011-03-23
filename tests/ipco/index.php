<?php
define('NWATENA', true);
require_once '../../base/watena.php';

Encoding::init('UTF-8');

require_once 'ipco.php';
require_once 'ipco_base.php';
require_once 'ipco_parser.php';
require_once 'ipco_processor.php';
require_once 'ipco_expression.php';
require_once 'ipco_componentwrapper.php';
require_once 'ipco_parsersettings.php';


class Tester {
	
	public $mPublic = array(true, false);
	private $mPrivate = true;
	public static $sPublic = true;
	private static $sPrivate = true;
	
	public $TEXT = 'dfmsvjisldkjfdghmfjk';
	
	public function getForeach() {
		return array(array('value' => 'man'), array('value' => 'woman'));
	}
	
	public function getMPublic($b) {return !$b;}
	private function getMPrivate() {}
	public static function getSPublic() {return 'B';}
	private static function getSPrivate() {}
}

/*
$aList = get_class_methods($oTester);
print_r($aList);
foreach($aList as $sMethod) {
	echo call_user_func(array($oTester, $sMethod));
}
exit;
*/

/*
echo new IPCO_Expression('-1 + (!(-2*\'a\')) is not 2^7 & {12, 3, 9, 8+2} & 8 - 0 & 3 && \'1\\\'\\\'2\' > 3+8 AND !8 + 2 OR 3', new IPCO());
echo "\n<br />\n";
echo new IPCO_Expression('substr(1, 2)', new IPCO());
echo "\n<br />\n";
echo new IPCO_Expression('test.substr(1, pow(2, 5) + 5 * 9)[2, \'test\']', new IPCO());
exit;
*/

$oTester = new Tester();
$oTester->mPublic[1] = $oTester;

$ipco = new IPCO();
echo $ipco->load('source', $oTester);
?>