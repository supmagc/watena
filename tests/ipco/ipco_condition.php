<?php

class IPCO_Condition extends IPCO_Base {

	const STATE_DEFAULT 	= 0;
	const STATE_QUOTE 		= 1;
	const STATE_BQUOTE 		= 2;
	const STATE_NUMERIC 	= 3;
	const STATE_DNUMERIC 	= 4;
	const STATE_END 		= 5;
	
	const CSTATE_ALL	= 0; // All are allowed
	const CSTATE_NOT	= 1; // We just passed a NOT
	const CSTATE_END	= 2; // We are at the end of a variable
	const CSTATE_OPEN	= 3; // We are after an opening brace
	
	const SEQUENCE_CONTROL		= '()&|!<>=+-*/%';
	const SEQUENCE_NUMERIC		= '1234567890';
	
	private $m_sMessage;
	
	public function __construct($sCondition, IPCO $ipco) {
		$nBraceCount = 0;
		$nState = self::STATE_DEFAULT;
		$nControlState = self::CSTATE_ALL;
		$nLength = Encoding::length($sCondition);
		$sResult = '';
		
		for($i=0 ; $i<$nLength ; ++$i) {
			$char = Encoding::substring($sCondition, $i, 1);
			$bAdd = true;
			
			if($char === '(') ++$nBraceCount;
			if($char === ')') --$nBraceCount;
			
			do {
				$bReparse = false;
				switch($nState) {
					case self::STATE_DEFAULT :
						if($char === '\'') {
							$nState = self::STATE_QUOTE;
						}
						else if(Encoding::indexOf(self::SEQUENCE_NUMERIC, $char) !== false) {
							$nState = self::STATE_NUMERIC;
						}
						else if(Encoding::indexOf(self::SEQUENCE_CONTROL, $char) !== false) {
							switch($nControlState) {
								case self::CSTATE_ALL : 
									if($char === '!') $nControlState = self::CSTATE_NOT;
									else if($char === '(') $nControlState = self::CSTATE_OPEN;
									break;
								case self::CSTATE_NOT : 
									if($char !== '=' && $char !== '(') $this->_noteProblem($i, 'Only opening braces of equality are allowed.');
									break;
								case self::CSTATE_END : 
									if($char === ')') $nControlState = self::CSTATE_ALL;
									else if($char === '(') $this->_noteProblem($i, 'No opening braces are allowed.');
									break;
								case self::CSTATE_OPEN : 
									if($char !== '(' && $char !== '!') $this->_noteProblem($i, 'Only opening braces of \'not\' are allowed.');
									break;
							}
							$char = $this->_modifyControlChar($char);
						}
						else if($char !== ' ') {
							$this->_noteProblem($i, 'Invalid character found.');
						}
						break;
						
					case self::STATE_QUOTE :
						if($char === '\'') {
							$nControlState = self::CSTATE_END;
						}
						else if($char === '\\') {
							$nState = self::STATE_BQUOTE;
						}
						break;
						
					case self::STATE_BQUOTE :
						$nState = self::STATE_QUOTE;
						break;
						
					case self::STATE_NUMERIC :
						if($char === '.') {
							$nState = self::STATE_DNUMERIC;
						}
						else if($char === ' ') {
							$nState = self::STATE_END;
						}
						else if(Encoding::indexOf(self::SEQUENCE_CONTROL, $char) !== false) {
							$bReparse = true;
							$nState = self::STATE_END;
						}
						else if(Encoding::indexOf(self::SEQUENCE_NUMERIC, $char) === false) {
							$this->_noteProblem($i, 'Invalid character after numeric value.');
						}
						break;
						
					case self::STATE_DNUMERIC :
						if($char === ' ') {
							$nState = self::STATE_END;
						}
						else if(Encoding::indexOf(self::SEQUENCE_CONTROL, $char) !== false) {
							$bReparse = true;
							$nState = self::STATE_END;
						}
						else if(Encoding::indexOf(self::SEQUENCE_NUMERIC, $char) === false) {
							$this->_noteProblem($i, 'Invalid character after numeric value.');
						}
						break;
				}
				
				if($nState === self::STATE_END) {
					$nControlState = self::CSTATE_END;
					$nState = self::STATE_DEFAULT;
				}
			} while($bReparse);
			
			if($bAdd) {
				$sResult .= $char;
			}
		}
		if($nBraceCount != 0) {
			$this->_noteProblem(-1, 'Invalid braces.');
		}
		echo $sResult;
	}
	
	private function _modifyControlChar($sChar) {
		if($sChar === '&') $sChar = '&&';
		if($sChar === '|') $sChar = '||';
		if($sChar === '=') $sChar = '==';
		return $sChar !== ' ' ? " $sChar " : $sChar;
	}
	
	private function _noteProblem($nColumn, $sMessage) {
		echo $nColumn;
		die($sMessage);
	}
}

?>