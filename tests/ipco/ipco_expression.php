<?php

class IPCO_Expression extends IPCO_Base {
	
	private $m_sOriginalExpression;
	private $m_sCleanedExpression;
	private $m_aOperators = array(' or ', '||', '|', ' and ', '&&', '&', '<', '>', '<=', '>=', '!=', '==', '=', '-', '+', '/', '*', '%', '^');
	
	public function __construct($sExpression, IPCO $ipco) {
		$this->m_sOriginalExpression = $sExpression;
		$this->m_sCleanedExpression = $this->_parseExpression($sExpression);
	}
	
	public function __toString() {
		return $this->m_sCleanedExpression;
	}
		
	private function _setError($sMessage, $sExpression) {
		die("$sMessage <<$sExpression>>");
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
		$nState = 0;
		$nIndex = $nLength + 1;
		for($i=0 ; $i<$nLength ; ++$i) {
			$char = Encoding::stringToLower(Encoding::substring($sExpression, $i, 1));
			$chars = Encoding::stringToLower(Encoding::substring($sExpression, $i, $nOperatorLength));
			switch($nState) {
				case 0 : 
					if(in_array($char, array('(', '[', '{'))) ++$nParentheses;
					else if(in_array($char, array(')', ']', '}'))) --$nParentheses;
					else if($nParentheses === 0) {
						if($chars === $sOperator && $this->_isOperator($sOperator, $sExpression, $i)) $nIndex = $i;
						else if($char === '\'') $nState = 1;
					}
					break;
				case 1 : 
					if($char === '\'') $nState = 0;
					else if($char === '\\') $nState = 2;
					break;
				case 2 : 
					$nState = 1;
					break;
			}
		}
		if($nState !== 0 || $nParentheses !== 0) $this->_setError("Invalid expression found.", $sExpression);
		return $nIndex > $nLength ? -1 : $nIndex;
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
	
	private function _getPhpExpression($sOperator, $sLeft, $sRight) {
		switch($sOperator) {
			case '^' : return "pow($sLeft, $sRight)"; 
			default : return "($sLeft ".$this->_getPhpOperator($sOperator)." $sRight)";
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
					if(in_array($char, array('(', '[', '{'))) ++$nParentheses;
					else if(in_array($char, array(')', ']', '}'))) --$nParentheses;
					else if($nParentheses === 0) {
						if($char === '\'') $nState = 1;
						elseif($char === ',') {
							$aParams []= Encoding::substring($sParams, $nMark, $i - $nMark);
							$nMark = $i + 1;
						}
					}
					break;
				case 1 : 
					if($char === '\'') $nState = 0;
					else if($char === '\\') $nState = 2;
					break;
				case 2 : 
					$nState = 1;
					break;
			}
		}
		if($nState !== 0 || $nParentheses !== 0) $this->_setError("Invalid string-sequence found in parameter list.", $sParams);
		$aParams []= Encoding::substring($sParams, $nMark, $nLength - $nMark);
		return implode(', ', array_map(array($this, '_parseExpression'), $aParams));
	}
	
	private function _parseCall($sExpression, $sBase = null) {
		$sExpression = Encoding::trim($sExpression);
		$nLength = Encoding::length($sExpression);
		$nState = -1;
		$nMark = 0;
		$sName = null;
		$sReturn = '';
		for($i=0 ; $i<$nLength ; ++$i) {
			$char = Encoding::substring($sExpression, $i, 1);
			switch($nState) {
				case -1:
					if($char === '{') {
						$nState = 7;
					}
					else $nState = 0;
				case 0: 
					if(in_array($char, array('(', '[', '{'))) {
						if($char === '(') $nState = 1;
						if($char === '[') $nState = 4;
						$nMark = $i + 1;
						$sName = Encoding::substring($sExpression, 0, $i);
					}
					else if(true) {} // TODO: character checking
					if($char === '.') {
						$sReturn = $this->_parseCall(Encoding::substring($sExpression, $i + 1), $sReturn);
					}
					break;
				case 1: 
					if($char === ')') {
						$sReturn .= 'parent::callMethod(\''.$sName.'\', array('.$this->_parseParameters(Encoding::substring($sExpression, $nMark, $i - $nMark)).'), '.$sBase.')';
						$nMark = $i + 1;
						$nState = 0;
					}
					if($char === '\'') $nState = 2;
					break;
				case 4: 
					if($char === ']') {
						$sReturn .= $sName . '[' . $this->_parseExpression(Encoding::substring($sExpression, $nMark, $i - $nMark)) . ']';
						$nMark = $i + 1;
						$nState = 0;
					}
					if($char === '\'') $nState = 5;
					break;
				case 7: 
					if($char === '}') {
						$sReturn .= 'array('.$this->_parseParameters(Encoding::substring($sExpression, 1, $i - $nMark)).')';
						$nMark = $i + 1;
						$nState = 0;
					}
					if($char === '\'') $nState = 8;
					break;
				case 2:
				case 5:
				case 8:
					if($char === '\\') {
						++$nState;
					}
					if($char === '\'') {
						--$nState;
					}
					break;
				case 3:
				case 6:
				case 9:
					--$nState;
					break;
			}
		}
		return $sExpression;
	}
	
	private function _parseValue($sExpression) {
		$sExpression = Encoding::trim($sExpression);
		$nLength = Encoding::length($sExpression);
		if($nLength === 0) {
			$this->_setError('Whitespace value detected, you might have an invalid double operator sequence.', $sExpression);
		}
		if($sExpression === '\'') {
			$this->_setError('SIngle quote detected without meaning.', $sExpression);
		}
		else {
			// easy primitive
			if($sExpression === 'true' || $sExpression === 'false' || Encoding::regMatch('^-?[0-9]+(\.[0-9]+)?$', $sExpression)) {
				return $sExpression;
			}
			// Look for string
			else if(Encoding::beginsWith($sExpression, '\'') && Encoding::endsWith($sExpression, '\'')) {
				$bEscaped = false;
				for($i=1 ; $i<$nLength - 1 ; ++$i) {
					$char = Encoding::substring($sExpression, $i, 1);
					if($bEscaped) $bEscaped = false;
					else {
						if($char === '\'') $this->_setError('Invalid none escaped quote found in string sequence.', $sExpression);
						else if($char === '\\') $bEscaped = true;
					}
				}
				return $sExpression;
			}
			// parsing for variablle, and or calling stuff
			else {
				return $this->_parseCall($sExpression);
			}
		}
	}
	
	private function _parseExpression($sExpression) {
		$sExpression = Encoding::trim($sExpression);
		$nLength = Encoding::length($sExpression);
		// Search for default operators
		for($i=0 ; $i<count($this->m_aOperators) ; ++$i) {		
			$nPos = $this->_findLastOccurance($this->m_aOperators[$i], $sExpression);
			$nOperatorLength = Encoding::length($this->m_aOperators[$i]);	
			if($nPos > -1) {
				return $this->_getPhpExpression($this->m_aOperators[$i], $this->_parseExpression(Encoding::substring($sExpression, 0, $nPos)), $this->_parseExpression(Encoding::substring($sExpression, $nPos + $nOperatorLength, $nLength - $nPos - $nOperatorLength)));
			}
		}
		// Recursivly parse within quotes
		if(Encoding::beginsWith($sExpression, '(') && Encoding::endsWith($sExpression, ')')) {
			return $this->_parseExpression(Encoding::substring($sExpression, 1, $nLength - 2));
		}
		// Recursivly remove negation sign
		if(Encoding::beginsWith($sExpression, '!')) {
			return '!' . $this->_parseExpression(Encoding::substring($sExpression, 1));
		}
		// If nothing found, parse as value
		return $this->_parseValue($sExpression);
	}
}

?>