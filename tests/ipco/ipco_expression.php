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
	
	private function _continuePastString($sExpression, $nIndex, $bCalculateParentheses = false) {
		$nState = 0;
		$nLength = Encoding::length($sExpression);
		$nParetheses = 0;
		if($nLength === 0) return $nIndex;
		for($i=$nIndex ; $i<$nLength ; ++$i) {
			$char = Encoding::substring($sExpression, $i, 1);
			switch($nState) {
				case 0:
					if($bCalculateParentheses) {
						if(in_array($char, array('(', '{', '['))) ++$nParetheses;
						if(in_array($char, array(')', '}', ']'))) --$nParetheses;
					}
					if($nParetheses === 0) $nState = $char === '\'' ? 1 : 3;
					break;
				case 1:
					if($char === '\\') $nState = 2;
					else if($char === '\'') $nState = 3;
					break;
				case 2:
					$nState = 1;
					break;
			}
			if($nState === 3) return $char === '\'' ? $i + 1 : $i;
		}
		$this->_setError('Invalid string sequence.', $sExpression);
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
	
	private function _parseParameters($sParams, $bImplode = true) {
		$aParams = array();
		$nLength = Encoding::length($sParams);
		if($nLength === 0) return $bImplode ? '' : array();
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
		$aParams = array_map(array($this, '_parseExpression'), $aParams);
		return $bImplode ? implode(', ', $aParams) : $aParams;
	}
	
	// TODO: stille some problems with advanced parsing stuff
	private function _parseCall($sExpression, $mBase = null) {
		echo $sExpression . '<br />';
		$sExpression = Encoding::trim($sExpression);
		$nLength = Encoding::length($sExpression);
		$nState = 0;
		$nMark = 0;
		$sName = null;
		$sReturn = null;
		for($i=$mBase !== null ? 0 : 1 ; $i<$nLength ; ++$i) {
			$i = $this->_continuePastString($sExpression, $i);
			$char = Encoding::substring($sExpression, $i, 1);
			switch($nState) {
				case 0:
					if($sReturn === null && $i > 0 && ($char === '.' || $char === '[')) $sReturn = 'parent::parseValue(\''.Encoding::substring($sExpression, 0, $i).'\', '.($mBase === null ? 'null' : $mBase).')';
					if($sReturn !== null) return $this->_parseCall(Encoding::substring($sExpression, $char === '.' ? $i + 1 : $i), $sReturn);
					if($i === 0 && $char === '[') $nState = 3;
					if($i > 0 && $char === '(') {
						$sName = Encoding::substring($sExpression, 0, $i);
						$nMark = $i + 1;
						$nState = 1;
					}
					break;
				case 1:
					if($char === ')') {
						$sReturn = 'parent::parseMethod(\''.$sName.'\', 
							array('.$this->_parseParameters(Encoding::substring($sExpression, $nMark, $i - $nMark)).'), 
							'.($mBase === null ? 'null' : $mBase).')';
						$nState = 0;
					}
					break;
				case 3:
					if($char === ']') {
						$aParts = $this->_parseParameters(Encoding::substring($sExpression, 1, $i - 1), false);
						foreach($aParts as $sIndex) {
							$sReturn = 'parent::parseArray('.$sIndex.', '.($sReturn === null ? $mBase : $sReturn).')';
						}
						$nState = 0;
					}
					break;
			}
		}
		return $sReturn;	
	}
	
	private function _parseValue($sExpression) {
		$sExpression = Encoding::trim($sExpression);
		$nLength = Encoding::length($sExpression);
		if($nLength === 0) {
			$this->_setError('Whitespace value detected, you might have an invalid double operator sequence.', $sExpression);
		}
		if($sExpression === '\'') {
			$this->_setError('Single quote detected without meaning.', $sExpression);
		}
		else {
			// easy primitive
			if($sExpression === 'true' || $sExpression === 'false' || Encoding::regMatch('^-?[0-9]+(\.[0-9]+)?$', $sExpression)) {
				return $sExpression;
			}
			// Look for string
			else if($this->_continuePastString($sExpression, 0, false) === $nLength) {
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
		// Recursivly parse within ()
		if(Encoding::beginsWith($sExpression, '(') && Encoding::endsWith($sExpression, ')')) {
			return $this->_parseExpression(Encoding::substring($sExpression, 1, $nLength - 2));
		}
		// Recursivly parse within {}
		if(Encoding::beginsWith($sExpression, '{') && Encoding::endsWith($sExpression, '}')) {
			return 'array('.$this->_parseParameters(Encoding::substring($sExpression, 1, $nLength - 2)).')';
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