<?php

class IPCO_Expression extends IPCO_Base {
	
	private $m_sOriginalExpression;
	private $m_sCleanedExpression;
	private $m_aOperators = array(' or ', '||', '|', ' and ', '&&', '&', '<', '>', '<=', '>=', '!=', '==', '=', '-', '+', '/', '*', '%');	
	
	public function __construct($sExpression, IPCO $ipco) {
		$this->m_sOriginalExpression = $sExpression;
		$this->m_sCleanedExpression = $this->_parseExpression($sExpression);
	}
	
	public function __toString() {
		return $this->m_sCleanedExpression;
	}
		
	private function _setError($sMessage, $sExpression) {
		die($sMessage);
	}
	
	private function _isOperator($sOperator, $sExpression, $nPos) {
		if($sOperator === '-') {
			for($i=$nPos ; $i>=0 ; --$i) {
				if($i === 0) return false;
				for($j=0 ; $j<count($this->m_aOperators) ; ++$j) {
					$nOperatorLength = Encoding::length($this->m_aOperators[$j]);
					if($i - $nOperatorLength >= 0 && Encoding::stringToLower(Encoding::substring($sExpression, $i - $nOperatorLength, $nOperatorLength)) === $this->m_aOperators[$j]) {
						return false;
					}
				}
				if($sExpression[$i - 1] !== ' ') return true;
			}
			return false;
		}
		return true;
	}
	
	private function _findLastOccurance($sOperator, $sExpression) {
		$nLength = Encoding::length($sExpression);
		$nOperatorLength = Encoding::length($sOperator);
		$nParentheses = 0;
		$bNoString = true;
		for($i=$nLength - $nOperatorLength ; $i>= 0 ; --$i) {
			$char = Encoding::stringToLower(Encoding::substring($sExpression, $i, $nOperatorLength));
			if($bNoString) {
				if($char === '(') --$nParentheses;
				else if($char === ')') ++$nParentheses;
				else if($nParentheses === 0) {
					if($char === $sOperator && $this->_isOperator($sOperator, $sExpression, $i)) return $i;
					else if($char === '\'') $bNoString = false;
				}
			}
			else if($char === '\'') {
				$nSlashCount = 0;
				for($j = $i - 1; $j>= 0 ; ++$j) {
					if(Encoding::substring($sExpression, $j, 1) === '\\') ++$nSlashCount;
					else break;
				}
				if($nSlashCount % 2 === 0) {
					if($nSlashCount == 0) $bNoString = true;
					else $this->_setError('Invalid string sequence found');
				}
			}
		}
		return -1;
	}
	
	private function _getPhpOperator($sOperator) {
		switch($sOperator) {
			case ' and ' : return '&&'; 
			case ' or ' : return '||'; 
			case '=' : return '=='; 
			case '&' : return '&&'; 
			case '|' : return '||'; 
			default : return $sOperator;
		}
	}
	
	private function _parseParameters($sParams) {
		$aParams = array();
		$nLength = Encoding::length($sParams);
		$nParentheses = 0;
		$nState = 0;
		$nMark = 0;
		for($i=0 ; $i<$nLength ; ++$i) {
			$char = Encoding::substring($sParams, $i, 1);
			switch($nState) {
				case 0 : 
					if($char === '(') ++$nParentheses;
					else if($char === ')') --$nParentheses;
					else if($nParentheses === 0) {
						if($char === '\'') $nState = 1;
						else if($char === ',') {
							$aParams []= Encoding::substring($sParams, $nMark, $i - $nMark);
							$nMark = $i + 1;
						}
					}
					break;
				case 0 : 
					if($char === '\'') $nState = 0;
					else if($char === '\\') $nState = 2;
					break;
				case 0 : 
					$nState = 1;
					break;
			}
		}
		if($nState !== 0 || $nParentheses !== 0) $this->_setError("Invalid string-sequence found in parameter list.", $sParams);
		$aParams []= Encoding::substring($sParams, $nMark, $nLength - $nMark);
		return implode(', ', array_map(array($this, '_parseExpression'), $aParams));
	}
	
	private function _parseValue($sExpression) {
		$sExpression = Encoding::trim($sExpression);
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
	
	private function _parseExpression($sExpression) {
		$sExpression = Encoding::trim($sExpression);
		$nLength = Encoding::length($sExpression);
		while(Encoding::beginsWith($sExpression, '(') && Encoding::endsWith($sExpression, ')')) {
			$sExpression = Encoding::trim(Encoding::substring($sExpression, 1, $nLength - 2));
			$nLength = Encoding::length($sExpression);
		}
		for($i=0 ; $i<count($this->m_aOperators) ; ++$i) {		
			$nPos = $this->_findLastOccurance($this->m_aOperators[$i], $sExpression);
			$nOperatorLength = Encoding::length($this->m_aOperators[$i]);	
			if($nPos > -1) {
				return '(' . $this->_parseExpression(Encoding::substring($sExpression, 0, $nPos)) . ' ' . $this->_getPhpOperator($this->m_aOperators[$i]) . ' ' . $this->_parseExpression(Encoding::substring($sExpression, $nPos + $nOperatorLength, $nLength - $nPos - $nOperatorLength)) . ')';
			}
		}
		return $this->_parseValue($sExpression);
	}
}

?>