<?php
function func($k, $v) {
	return $v;
}
$a = array(true, false);
var_dump(array_walk($a, 'func'));
?>