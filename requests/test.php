<?php
include '../base/watena.php';
WatenaLoader::init();

$a = 10;
$b = &$a;
echo "$a - $b<br />";
$b += 10;
echo "$a - $b<br />";
$a += 10;
echo "$a - $b<br />";
$a = null;
echo "$a - $b<br />";

class Base {
	
	private function __construct($s) {
		echo 'DONE: '.$s;
	}
	
	public function who() {
		echo 'BASE';
	}
	
	public static function Test() {
		return new static('Hello World');
	}
}

class Child extends Base {

	public function who() {
		echo 'CHILD';
	}
}

$oInstance = Child::Test();
echo ' '.$oInstance->who();
?>