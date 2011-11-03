<?php

class Base {

	private $a = '1';
	public $b = '2';
	
	public function serializable() {
		$aVariables []= 'a';
		$aVariables []= 'b';
		echo 'BASE';
		return array('a', 'b');
	}
	
	public final function __sleep() {
		$aVariables = array();
		$aClasses = class_parents($this);
		$aClasses[get_class($this)] = get_class($this);
		foreach($aClasses as $sClass) {
			$oClass = new ReflectionClass($sClass);
			if($oClass->getMethod('serializable') != null) {
				$a = $sClass::serializable();
				if(is_array($a)) {
					$aVariables = array_merge($aVariables, $a);
				}
			}
		}
		var_dump($aClasses);
		var_dump($aVariables);
		return $aVariables;
	}
	
	public function setABase($a) {$this->a = $a;}
}

class Child extends Base {
	
	private $a = '3';
	public $c = '4';
	
	public function serializable() {
		$aVariables []= 'a';
		$aVariables []= 'c';
		echo 'CHILD';
		return array('a', 'c');
	}
	
	public function setAChild($a) {$this->a = $a;}
}

$o = new Child();
$o->setABase(10);
$o->setAChild(100);
$o->b = 11;
$o->c = 12;
var_dump(serialize($o));
var_dump(unserialize(serialize($o)));
?>