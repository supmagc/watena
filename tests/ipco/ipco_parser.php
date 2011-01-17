<?php

class IPCO_Parser extends IPCO_Base {
	
	const STATE_DEFAULT 	= 0;
	const STATE_IPCO 		= 1;
	const STATE_IPCO_QUOTE 	= 3;
	const STATE_IPCO_VAR 	= 4;
	const STATE_IPCO_BQUOTE	= 5;
	
	private $m_sContent;
	
	public function __construct($sFileName, IPCO $ipco) {
		parent::__construct($ipco);
		$this->m_sContent = file_get_contents(parent::getIpco()->getSourcePath($sFileName));
	}
	
	public function getHeader() {
		return '
<?php
class _Source_CV extends IPCO_Compiled {

	public function __construct() {
		$_ob = \'\';
		$_comp = null;';
	}
	
	public function getFooter() {
		return '
		echo $_ob;
	}
}
?>';
	}
	
	public function parse() {
		// While characters found
		// Read next character
		// If state is default, check if start of IPCO
			// flush untill marker, set new marker

		$nMark = 0;
		$aBuffer = array($this->getHeader());
		$nState = self::STATE_DEFAULT;
		$nLength=Encoding::length($this->m_sContent);
		
		
		
		for($i=0 ; $i<$nLength ; ++$i) {
			
			$char1 = Encoding::substring($this->m_sContent, $i, 1);
			$char2 = Encoding::substring($this->m_sContent, $i, 2);
			
			switch($nState) {
				case self::STATE_DEFAULT : 
					if($char2 === '{%') {
						$aBuffer []= $this->compileContent(Encoding::substring($this->m_sContent, $nMark, $i-$nMark));
						$nMark = $i += 2;
						$nState = self::STATE_IPCO;
					}
					else if($char2 === '{{') {
						$aBuffer []= $this->compileContent(Encoding::substring($this->m_sContent, $nMark, $i-$nMark));
						$nMark = $i += 2;
						$nState = self::STATE_IPCO_VAR;
					}
					break;
					
				case self::STATE_IPCO : 
					if($char2 === '%}') {
						$aBuffer []= $this->compileFilter(Encoding::substring($this->m_sContent, $nMark, $i-$nMark));
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
						$aBuffer []= $this->compileVariable(Encoding::substring($this->m_sContent, $nMark, $i-$nMark));
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
	
	public function compileContent($sContent) {
		return '
		$_ob .= \''.Encoding::trim($sContent).'\';';
	}
	
	public function compileFilter($sContent) {
		$aParts = array_map(array('Encoding', 'trim'), explode(' ', Encoding::trim($sContent)));
		$sName = array_shift($aParts);
		switch($sName) {
			case 'if' : return $this->compileIf($aParts); break;
			case 'foreach' : return $this->compileForeach($aParts); break;
			case 'while' : return $this->compileWhile($aParts); break;
			case 'else' : return $this->compileElse($aParts); break;
			case 'elseif' : return $this->compileElseif($aParts); break;
			case 'end' : return $this->compileEnd($aParts); break;
			case 'component' : return $this->compileComponent($aParts); break;
			case 'template' : return $this->compileTemplate($aParts); break;
		}
	}
	
	public function compileVariable($sContent) {
		return '-variable-';
	}
	
	public function compileIf($aParts) {
		return <<<EOB
		if(true) {
EOB;
	}
	
	public function compileForeach($aParts) {
		return '
		foreach(array() as $a) {';
	}
	
	public function compileWhile($aParts) {
		return <<<EOB
		while(false) {
EOB;
	}
	
	public function compileElse($aParts) {
		return <<<EOB
		}
		else {
EOB;
	}
	
	public function compileElseif($aParts) {
		return <<<EOB
		}
		elseif(true) {
EOB;
	}
	
	public function compileEnd($aParts) {
		return <<<EOB
		}
EOB;
	}
	
	public function compileComponent($aParts) {
		return <<<EOB
EOB;
	}
	
	public function compileTemplate($aParts) {
		return <<<EOB
EOB;
	}
}

?>