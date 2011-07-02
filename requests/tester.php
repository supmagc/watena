<?php

class MyParent {
	
	private $var1; // serialise
	private $var2; // don't' serialise
	
	function __construct() {
		$this -> var1 = "Hello World";
		$this -> var2 = "Goodbye World";
	}
	
	function __sleep() {
		return array("var1");
	}
}

class MyChild extends MyParent {
	
	private $var3; // serialise
	private $var4; // don't serialise
	
	function __construct() {
		$this -> var3 = "Hello World II";
		$this -> var4 = "Goodbye World II";
	}
	
	function __sleep() {
		return array("var3");
	}
}

$mc = new MyChild();
$mcSerialised = serialize($mc);
print_r($mcSerialised);
?>