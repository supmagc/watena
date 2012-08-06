<?php

class IPCO_Expression extends IPCO_Base {
	
	const ERROR_UNKNOWN = 0;
	const ERROR_INVALIDSTRING = 1;
	const ERROR_INVALIDPARENTHESES = 2;
	const ERROR_INVALIDWHITESPACE = 3;
	const ERROR_INVALIDQUOTE = 4;
	
	private $m_sOriginalExpression;
	private $m_sCleanedExpression;
	private $m_aOperators = array(' or ', '||', '|', ' and ', '&&', '&', '<', '>', '<=', '>=', ' is not ', '!=', '<>', ' is ', '==', '=', '-', '+', '/', '*', '%', '^');
	private $m_aKeywords = array('key', 'index', 'value', 'first', 'last');
	
	public function __construct($sExpression, IPCO $ipco) {
		$this->m_sOriginalExpression = $sExpression;
		$this->m_sCleanedExpression = $this->_parseExpression($sExpression);
	}
	
	public function __toString() {
		return '' . $this->m_sCleanedExpression;
	}
		
	private function _setError($nCode, $sExpression) {
		debug_print_backtrace();
		switch($nCode) {
			case self::ERROR_UNKNOWN: throw new IPCO_ExpressionException("Unknown error.", $sExpression);
			case self::ERROR_INVALIDSTRING: throw new IPCO_ExpressionException("Invalid string sequence.", $sExpression);
			case self::ERROR_INVALIDPARENTHESES: throw new IPCO_ExpressionException("Invalid parentheses/braces.", $sExpression);
			case self::ERROR_INVALIDEXPRESSION: throw new IPCO_ExpressionException("Invalid expression.", $sExpression);
			case self::ERROR_INVALIDWHITESPACE: throw new IPCO_ExpressionException("Invalid whitespace.", $sExpression);
			case self::ERROR_INVALIDQUOTE: throw new IPCO_ExpressionException("Invalid quote.", $sExpression);
			default: exit;
		}
	}
	
	/**
	 * Check if the occurance of the specified operator within the specified expression
	 * is meant as an operator. This is required for operators who can occur both as
	 * operator and/or as value formatter.
	 * 
	 * @param string $sOperator
	 * @param string $sExpression
	 * @param string $nPos
	 * @return bool
	 */
	private function _isOperator($sOperator, $sExpression, $nPos) {
		switch($sOperator) {
			case '-':
				for($i=$nPos-1 ; $i>=0 ; --$i) {
					for($j=0 ; $j<count($this->m_aOperators) ; ++$j) {
						$nOperatorLength = Encoding::length($this->m_aOperators[$j]);
						if($i - $nOperatorLength >= 0 && Encoding::toLower(Encoding::substring($sExpression, $i - $nOperatorLength, $nOperatorLength)) === $this->m_aOperators[$j]) {
							return false;
						}
					}
					if($i > 0 && $sExpression[$i - 1] !== ' ') return true;
				}
				return false;
			default: return true;
		}
	}

	/**
	 * Proceed the given index passed any possible string value within the provided
	 * expression sequence.
	 * It's possible to automatically pass all found parentheses.
	 * 
	 * @param string $sExpression
	 * @param int $nIndex
	 * @param bool $bCalculateParentheses
	 * @return int The new index
	 */
	private function _continuePastString($sExpression, $nIndex, $bCalculateParentheses = true) {
		$nLength = Encoding::length($sExpression);
		if($nLength === 0 || $nIndex >= $nLength) return $nIndex;
		
		$nState = 0;
		$nParentheses = 0;
		for($i=$nIndex ; $i<$nLength ; ++$i) {
			$char = Encoding::substring($sExpression, $i, 1);
			switch($nState) {
				case 0:
					if($bCalculateParentheses && in_array($char, array('(', '{', '['))) ++$nParentheses;
					else if($bCalculateParentheses && $nParentheses > 0 && in_array($char, array(')', '}', ']'))) --$nParentheses;
					else if($nParentheses === 0) {
						if($char === '\'') $nState = 1;
						else return $i;
					}
					break;
				case 1:
					if($char === '\'') $nState = 0;
					else if($char === '\\') $nState = 2;
					break;
				case 2:
					$nState = 1;
			}
		}
		if($nParentheses !== 0) $this->_setError(self::ERROR_INVALIDPARENTHESES, Encoding::substring($sExpression, $nIndex));
		if($nState !== 0) $this->_setError(self::ERROR_INVALIDSTRING, Encoding::substring($sExpression, $nIndex));
		return $nLength;
	}
	
	/**
	 * Find the last occurance of the given operator in the given expression.
	 * The return value will be the position-index if found, or -1.
	 * 
	 * @param string $sOperator
	 * @param string $sExpression
	 */
	private function _findLastOccurance($sOperator, $sExpression) {
		$nLength = Encoding::length($sExpression);
		$nOperatorLength = Encoding::length($sOperator);
		$nState = 0;
		$nIndex = $nLength + 1;
		for($i=0 ; ($i = $this->_continuePastString($sExpression, $i, true))<$nLength ; ++$i) {
			$i = $this->_continuePastString($sExpression, $i);
			$char = Encoding::toLower(Encoding::substring($sExpression, $i, 1));
			$chars = Encoding::toLower(Encoding::substring($sExpression, $i, $nOperatorLength));
			if($chars === $sOperator && $this->_isOperator($sOperator, $sExpression, $i)) $nIndex = $i;
		}
		return $nIndex > $nLength ? -1 : $nIndex;
	}
	
	/**
	 * Returns the correct php-operator that matches the IPCO operator.
	 * 
	 * @param string $sOperator
	 * @return string
	 */
	private function _getPhpOperator($sOperator) {
		switch($sOperator) {
			case ' and ' : return '&&'; 
			case ' or ' : return '||'; 
			case ' is ' : return '=='; 
			case ' is not ' : return '!='; 
			case '<>' : return '!='; 
			case '=' : return '=='; 
			case '&' : return '&&'; 
			case '|' : return '||'; 
			default : return $sOperator;
		}
	}
	
	/**
	 * Returns the correct php-expression that matches the IPCO operator and before-/after-sequences
	 * 
	 * @param string $sOperator
	 * @param string $sLeft
	 * @param string $sRight
	 * @return string
	 */
	private function _getPhpExpression($sOperator, $sLeft, $sRight) {
		switch($sOperator) {
			case '^' : return "pow($sLeft, $sRight)"; 
			case '+' :
				if(Encoding::endsWith($sLeft, '\'') || Encoding::beginsWith($sRight, '\'')) return "((\'\'.$sLeft) . (\'\'$sRight))";
			default : return "($sLeft ".$this->_getPhpOperator($sOperator)." $sRight)";
		}
	}

	/**
	 * Returns the correct php-call thet matches the input parameters as needed by IPCO
	 * 
	 * @param string $sName
	 * @param array $aParams
	 * @param array $aSlices
	 * @param mixed $mBase
	 */
	private function _getPhpCall($sName = null, array $aParams = array(), array $aSlices = array(), $sBase = 'null') {
		$sReturn = 'null';
		if(isset($sName)) {
			if(in_array($sName, $this->m_aKeywords))
				$sReturn = IPCO_ParserSettings::getCallKeyword($sName, $aParams, $sBase);
			else if(empty($aParams)) 
				$sReturn = IPCO_ParserSettings::getCallMember($sName, $sBase);
			else 				
				$sReturn = IPCO_ParserSettings::getCallMethod($sName, $aParams, $sBase);
		}
		foreach($aSlices as $sSlice) {
			$sReturn = IPCO_ParserSettings::getCallSlice($sSlice, $sReturn);
		}
		return $sReturn;
	}
	
	private function _parseListing($sSplitter, $sExpression) {
		$aParams = array();
		$nLength = Encoding::length($sExpression);
		if($nLength === 0) return array();
		$nParentheses = 0;
		$nState = 0;
		$nMark = 0;
		for($i=0 ; ($i = $this->_continuePastString($sExpression, $i))<$nLength ; ++$i) {
			$char = Encoding::substring($sExpression, $i, 1);
			if($char === $sSplitter) {
				$aParams []= Encoding::substring($sExpression, $nMark, $i - $nMark);
				$nMark = $i + 1;
			}
		}
		$aParams []= Encoding::substring($sExpression, $nMark, $nLength - $nMark);
		return $aParams;
	}
	
	private function _parseCall($sExpression, $mBase = null) {
		$sExpression = Encoding::trim($sExpression);
		$nLength = Encoding::length($sExpression);
		
		$nState = 1;
		$nMark = 0;
		$sName = null;
		$aParams = array();
		$aSlices = array();
		$i = 0;
		while($i<$nLength) {
			$char = Encoding::substring($sExpression, $i, 1);
			switch($nState) {
				case 1:
					if($char === '(') {
						$nState = 2;
						$sName = Encoding::substring($sExpression, $nMark, $i);
						$nMark = $i + 1;
					}
					else if($char === '[') {
						$nState = 4;
						$sName = Encoding::substring($sExpression, $nMark, $i);
						$nMark = $i + 1;
					}
					break;
				case 2:
					if($char === ')') {
						$nState = 3;
						$aParams = $this->_parseListing(',', Encoding::substring($sExpression, $nMark, $i - $nMark));
						$nMark = $i + 1;
					}
					break;
				case 3;
					if($char === '[') {
						$nState = 4;
						$nMark = $i + 1;
					}
				case 4:
					if($char === ']') {
						$nState = 3;
						$aSlices = array_merge($aSlices, $this->_parseListing(',', Encoding::substring($sExpression, $nMark, $i - $nMark)));
					}
					break;
			}
			++$i;
			if($nState === 2 || $nState === 4) $i = $this->_continuePastString($sExpression, $i);
		}
		if($nState === 1 && $sName === null) $sName = Encoding::substring($sExpression, 0, $nLength);
		$sName = Encoding::trim($sName);
		$aParams = array_map(array($this, '_parseExpression'), $aParams);
		$aSlices = array_map(array($this, '_parseExpression'), $aSlices);
		if($mBase === null) $mBase = 'null';

		return $this->_getPhpCall($sName, $aParams, $aSlices, $mBase);
	}
	
	private function _parseValue($sExpression) {
		$sExpression = Encoding::trim($sExpression);
		$nLength = Encoding::length($sExpression);
		
		// Edge case with empty valueexpression
		// This happens when an unbrecognisable whitespace is found.
		// Exmaple: double operator
		if($nLength === 0) {
			$this->_setError('Whitespace value detected, you might have an invalid double operator sequence.', $sExpression);
		}
		
		// Edge case with single quote.
		if($sExpression === '\'') {
			$this->_setError('Single quote detected without meaning.', $sExpression);
		}
		
		// All 'normal' cases.
		// By default in here we will check for primitives, strings and native arrays.
		// If those are not found, the parser will try to figure out if the value is a call.
		else {
			
			// Look for easy primitives
			if($sExpression === 'true' || $sExpression === 'false' || Encoding::regMatch('^-?[0-9]+(\.[0-9]+)?$', $sExpression)) {
				return $sExpression;
			}
			
			// Look for string
			else if(Encoding::beginsWith($sExpression, '\'')) {
				if($this->_continuePastString($sExpression, 0, false) === $nLength)
					return $sExpression;
				else
					return $this->_setError(self::ERROR_INVALIDSTRING, $sExpression);
			}
			
			// Look for array
			else if(Encoding::beginsWith($sExpression, '{') && Encoding::endsWith($sExpression, '}')) {
				$aParams = $this->_parseListing(',', Encoding::substring($sExpression, 1, $nLength - 2));
				return IPCO_ParserSettings::getCallArray(array_map(array($this, '_parseExpression'), $aParams));
			}
			
			// parsing for calling stuff
			else {
				$aCalls = $this->_parseListing('.', $sExpression);
				$sReturn = null;
				foreach($aCalls as $sCall) {
					$sReturn = $this->_parseCall($sCall, $sReturn);
				}
				return $sReturn;
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
		
		// If no valid operator was found, check if the entire expression
		// is encapsulated within braces. If so, we know they are outer
		// (Inner braces must have been concatenated with an operator) and
		// we can safly remove them. At this stage we return the parsed 
		// inner-expression.
		if(Encoding::beginsWith($sExpression, '(') && Encoding::endsWith($sExpression, ')')) {
			return $this->_parseExpression(Encoding::substring($sExpression, 1, $nLength - 2));
		}
		
		// If no expression construction can be found, check if this part
		// is negated with a negation sign. If so, remove it and recursivly
		// parse the remaining expression.
		if(Encoding::beginsWith($sExpression, '!')) {
			return '!' . $this->_parseExpression(Encoding::substring($sExpression, 1));
		}
		if(Encoding::beginsWith($sExpression, 'not ')) {
			return '!' . $this->_parseExpression(Encoding::substring($sExpression, 4));
		}
		
		// If nothing found, parse as value
		return $this->_parseValue($sExpression);
	}
}

?>