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


	public function __construct() { }

	public function __toString() {
		$_ob = \'\';
';
	}
	
	public function getFooter() {
		return '	
		return $_ob;
	}
}
?>';
	}
	
	public function parse() {
		$this->m_nDepth = 0;
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
					else if($char2 === '{{') {
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
					if($char2 === '}}') {
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
		return '-variable-';
	}
	
	public function interpretIf(IPCO_Expression $oCondition) {
		return $this->getDepthOffset(0, 1) . "if($oCondition) {\n";
	}

	/*
	public function interpretForeach(IPCO_Expression $oData) {
		return $this->getDepthOffset(0, 1) . "foreach($oData as $_comp) {\n";
	}
	*/
	
	public function interpretWhile(IPCO_Expression $oCondition) {
		return $this->getDepthOffset(0, 1) . "while($oCondition) {\n";
	}
	
	public function interpretElse() {
		return $this->getDepthOffset(-1, 1) . "} else {\n";
	}
	
	public function interpretElseIf(IPCO_Expression $oCondition) {
		return $this->getDepthOffset(-1, 1) . "} elseif($oCondition) {\n";
	}
	
	public function interpretEnd($aParts) {
		return $this->getDepthOffset(-1, 0) . "}\n";
	}
	
	public function getDepthOffset($nPreChange = 0, $nPostChange = 0) {
		$sReturn = "\t\t";
		$this->m_nDepth += $nPreChange;
		for($i=0 ; $i<$this->m_nDepth ; ++$i) {
			$sReturn .= "\t";
		}
		$this->m_nDepth += $nPostChange;
		return $sReturn;
	}
}

?>