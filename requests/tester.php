<?php

class MyClass {
	
	private $priva;
	var $publi = 'member';
	const CONSTVAR = 1;
	private static $spriva;
	static $spubli = 'static';
}

$oClass = new MyClass();
$oReflector = new ReflectionClass($oClass);

var_dump(ReflectionProperty::IS_PUBLIC);
var_dump(ReflectionProperty::IS_STATIC);

print_r($oReflector->getConstants());
print_r($oReflector->getProperties(ReflectionProperty::IS_PUBLIC));
print_r($oReflector->getProperties(ReflectionProperty::IS_STATIC));

$aProp = $oReflector->getProperties(ReflectionProperty::IS_PUBLIC);
foreach($aProp as $oProp) {
	var_dump($oProp->getValue($oClass));
}
?>