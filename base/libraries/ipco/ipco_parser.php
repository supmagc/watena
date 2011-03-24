<?php

class IPCO_Parser extends IPCO_Base {
	
	const STATE_DEFAULT 	= 0;
	const STATE_IPCO 		= 1;
	const STATE_IPCO_QUOTE 	= 3;
	const STATE_IPCO_VAR 	= 4;
	const STATE_IPCO_BQUOTE	= 5;
	
	private $m_sIdentifier;
	private $m_sSourcePath;
	private $m_sClassName;
	private $m_sContent;
	private $m_nDepth;
	private $m_aEndings;
	
	public function __construct($sIdentifier, IPCO $ipco) {
		parent::__construct($ipco);
		$this->m_sIdentifier = $sIdentifier;
		$this->m_sSourcePath = parent::getIpco()->getSourcePath($sIdentifier);
		$this->m_sClassName = parent::getIpco()->getClassName($sIdentifier);
		$this->m_sContent = file_get_contents(parent::getIpco()->getSourcePath($sIdentifier));
	}
	
	public function getIdentifier() {
		return $this->m_sIdentifier;
	}
	
	public function getSourcePath() {
		return $this->m_sSourcePath;
	}
	
	public function getClassName() {
		return $this->m_sClassName;
	}
	
	public function getHeader() {
		return '
<?php
class '.$this->m_sClassName.' extends IPCO_Processor {

	public function __toString() {
		try {
			$_ob = \'\';
';
	}
	
	public function getFooter() {
		return '
			return $_ob;
		}
		catch(Exception $e) {
			return $e;
		}
	}
}
?>';
	}
	
	public function parse() {
		$this->m_nDepth = 0;
		$this->m_aEndings = array();
		$nMark = 0;
		$aBuffer = array($this->getHeader());
		$nState = self::STATE_DEFAULT;
		$nLength = Encoding::length($this->m_sContent);
 		
		for($i=0 ; $i<$nLength ; ++$i) {
			
			$char1 = Encoding::substring($this->m_sContent, $i, 1);
			$char2 = Encoding::substring($this->m_sContent, $i, 2);
			
			switch($nState) {
				case self::STATE_DEFAULT : 
					if($char2 === '{%') {
						$aBuffer []= $this->interpretContent(Encoding::substring($this->m_sContent, $nMark, $i-$nMark));
						$nMark = $i += 2;
						$nState = self::STATE_IPCO;
					}
					else if($char2 === '{[') {
						$aBuffer []= $this->interpretContent(Encoding::substring($this->m_sContent, $nMark, $i-$nMark));
						$nMark = $i += 2;
						$nState = self::STATE_IPCO_VAR;
					}
					break;
					
				case self::STATE_IPCO : 
					if($char2 === '%}') {
						$aBuffer []= $this->interpretFilter(Encoding::substring($this->m_sContent, $nMark, $i-$nMark));
						$nMark = $i += 2;
						$nState = self::STATE_DEFAULT;
					}
					else if($char1 === '\'') {
						$nState = self::STATE_IPCO_QUOTE;
					}
					break;
					
				case self::STATE_IPCO_QUOTE : 
					if($char1 === '\'') {
						$nState = self::STATE_IPCO;
					}
					else if($char1 === '\\') {
						$nState = self::STATE_BQUOTE;
					}
					break;
					
				case self::STATE_IPCO_VAR : 
					if($char2 === ']}') {
						$aBuffer []= $this->interpretVariable(Encoding::substring($this->m_sContent, $nMark, $i-$nMark));
						$nMark = $i += 2;
						$nState = self::STATE_DEFAULT;
					}
					break;
					
				case self::STATE_IPCO_BQUOTE : 
					$nState = self::STATE_IPCO_QUOTE;
					break;
			}
		}
		$aBuffer []= Encoding::substring($this->m_sContent, $nMark, $nLength-$nMark);
		$aBuffer []= $this->getFooter();
		
		return implode('', $aBuffer);
	}
	
	public function interpretContent($sContent) {
		return $this->getDepthOffset() . '$_ob .= \''.$sContent.'\';'."\n";
	}
	
	public function interpretFilter($sContent) {
		$aParts = array_map(array('Encoding', 'trim'), explode(' ', Encoding::trim($sContent)));
		$sName = array_shift($aParts);
		switch($sName) {
			case 'if' : return $this->interpretIf(new IPCO_Expression(implode(' ', $aParts), parent::getIpco())); break;
			case 'foreach' : return $this->interpretForeach(new IPCO_Expression(implode(' ', $aParts), parent::getIpco())); break;
			case 'while' : return $this->interpretWhile(new IPCO_Expression(implode(' ', $aParts), parent::getIpco())); break;
			case 'else' : return $this->interpretElse(); break;
			case 'elseif' : return $this->interpretElseIf(new IPCO_Expression(implode(' ', $aParts), parent::getIpco())); break;
			case 'end' : return $this->interpretEnd(count($aParts) > 0 ? $aParts[0] : null); break;
			case 'component' : return $this->interpretComponent($aParts); break;
			case 'template' : return $this->interpretTemplate($aParts); break;
		}
	}
	
	public function interpretVariable($sContent) {
		$sCondition = new IPCO_Expression($sContent, parent::getIpco());
		return $this->getDepthOffset() . '$_ob .= '.$sCondition.";\n";
	}
	
	public function interpretIf(IPCO_Expression $oCondition) {
		array_push($this->m_aEndings, 'if');
		return $this->getDepthOffset(0, 1) . IPCO_ParserSettings::getFilterIf($oCondition->__toString());
	}
	
	public function interpretElseIf(IPCO_Expression $oCondition) {
		return $this->getDepthOffset(-1, 1) . IPCO_ParserSettings::getFilterElseIf($oCondition->__toString());
	}
	
	public function interpretElse() {
		return $this->getDepthOffset(-1, 1) . IPCO_ParserSettings::getFilterElse();
	}
	
	public function interpretForeach(IPCO_Expression $oCondition) {
		array_push($this->m_aEndings, 'foreach');
		return $this->getDepthOffset(0, 1) . IPCO_ParserSettings::getFilterForeach($oCondition);
	}
	
	public function interpretWhile(IPCO_Expression $oCondition) {
		array_push($this->m_aEndings, 'while');
		return $this->getDepthOffset(0, 1) . "while($oCondition) {\n";
	}
	
	public function interpretEnd($aParts) {
		$sExpectedEnding = array_pop($this->m_aEndings);
		$sGivenEnding = Encoding::stringToLower(Encoding::trim($aParts));
		if(Encoding::length($sGivenEnding) > 0 && $sExpectedEnding != $sGivenEnding) {
			throw new IPCO_Exception('Invalid IPCO-tag nesting.', IPCO_Exception::INVALIDNESTING);
		}
		switch($sExpectedEnding) {
			case 'if' : return $this->getDepthOffset(-1, 0) . IPCO_ParserSettings::getFilterEndIf();
			case 'foreach' : return $this->getDepthOffset(-1, 0) . IPCO_ParserSettings::getFilterEndForeach();
		}
		return '';
	}
	
	public function getDepthOffset($nPreChange = 0, $nPostChange = 0) {
		$sReturn = "\t\t\t";
		$this->m_nDepth += $nPreChange;
		for($i=0 ; $i<$this->m_nDepth ; ++$i) {
			$sReturn .= "\t";
		}
		$this->m_nDepth += $nPostChange;
		return $sReturn;
	}
}

?>