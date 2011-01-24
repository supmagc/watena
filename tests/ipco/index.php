<?php

require_once '../../base/classes/static.encoding.php';
Encoding::init('UTF-8');

require_once 'ipco.php';
require_once 'ipco_base.php';
require_once 'ipco_parser.php';
require_once 'ipco_compiled.php';
require_once 'ipco_expression.php';

/*
$aOperators = array(' or ', '||', '|', ' and ', '&&', '&', '<', '>', '<=', '>=', '!=', '==', '=', '-', '+', '/', '*', '%');

function setError($sMessage) {
	die($sMessage);
}

function isOperator($sOperator, $sExpression, $nPos) {
	global $aOperators;
	if($sOperator === '-') {
		for($i=$nPos ; $i>=0 ; --$i) {
			if($i === 0) return false;
			for($j=0 ; $j<count($aOperators) ; ++$j) {
				$nOperatorLength = Encoding::length($aOperators[$j]);
				if($i - $nOperatorLength >= 0 && Encoding::stringToLower(Encoding::substring($sExpression, $i - $nOperatorLength, $nOperatorLength)) === $aOperators[$j]) {
					return false;
				}
			}
			if($sExpression[$i - 1] !== ' ') return true;
		}
		return false;
	}
	return true;
}

function findLastOccurance($sOperator, $sExpression) {
	$nLength = Encoding::length($sExpression);
	$nOperatorLength = Encoding::length($sOperator);
	$nParentheses = 0;
	for($i=$nLength - $nOperatorLength ; $i>= 0 ; --$i) {
		$char = Encoding::stringToLower(Encoding::substring($sExpression, $i, $nOperatorLength));
		if($char === '(') ++$nParentheses;
		else if($char === ')') --$nParentheses;
		else if($nParentheses === 0 && $char === $sOperator && isOperator($sOperator, $sExpression, $i)) return $i;
	}
	return -1;
}

function getPhpOperator($sOperator) {
	switch($sOperator) {
		case ' and ' : return '&&'; 
		case ' or ' : return '||'; 
		case '=' : return '=='; 
		case '&' : return '&&'; 
		case '|' : return '||'; 
		default : return $sOperator;
	}
}

function parseValue($sExpression) {
	$sExpression = Encoding::trim($sExpression);
	if(Encoding::beginsWith($sExpression, 'substr')) echo $sExpression;
	$bNot = false;
	if(Encoding::beginsWith($sExpression, '!')) {
		$bNot = true;
		$sExpression = Encoding::trim(Encoding::substring($sExpression, 1));
	}
	$nLength = Encoding::length($sExpression);
	if($nLength === 0) {
		setError('Whitespace value detected, you might have an invalid double operator sequence.');
	}
	else {
		return ($bNot ? '!' : '') . $sExpression;
	}
}

function parseExpression($sExpression) {
	global $aOperators;
	$sExpression = Encoding::trim($sExpression);
	$nLength = Encoding::length($sExpression);
	while(Encoding::beginsWith($sExpression, '(') && Encoding::endsWith($sExpression, ')')) {
		$sExpression = Encoding::trim(Encoding::substring($sExpression, 1, $nLength - 2));
		$nLength = Encoding::length($sExpression);
	}
	for($i=0 ; $i<count($aOperators) ; ++$i) {		
		$nPos = findLastOccurance($aOperators[$i], $sExpression);
		$nOperatorLength = Encoding::length($aOperators[$i]);	
		if($nPos > -1) {
			return '(' . parseExpression(Encoding::substring($sExpression, 0, $nPos)) . ' ' . getPhpOperator($aOperators[$i]) . ' ' . parseExpression(Encoding::substring($sExpression, $nPos + $nOperatorLength, $nLength - $nPos - $nOperatorLength)) . ')';
		}
	}
	return parseValue($sExpression);
}
*/

echo new IPCO_Expression('-1 + (!(2*a)) != 2 & 8 - substr(\'bla\', 1, 2) & 3 && \'12\' > 3+8 AND !8 + 2 OR 3', new IPCO());

exit;

// TODO: First parse and search for function calls and component calls etc ... we need a syntax for this
// TODO: create parseParameters($sParams)


new IPCO_Condition('(\'a\' & !(\'b\' | 12.56 = 3.8))', new IPCO());
//$ipco = new IPCO();
//$ipco->load('source');
?>