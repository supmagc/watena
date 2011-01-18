<?php

class IPCO_BuilderBlock extends IPCO_Base {
	
	private $m_aSubBlocks = array();
	private $m_oParentBlock;
	private $m_aBuffer = array();
	private $m_sName;
	
	public function __construct($sName, IPCO $ipco) {
		parent::__construct($ipco);
		$this->m_sName = $sName;
	}
	
	public function addToBuffer($sContent) {
		$this->m_aBuffer []= $sContent;
	}
	
	public function addSubBlock(IPCO_BuilderBlock $oBlock) {
		$this->m_aSubBlocks []= $oBlock;
		$oBlock->m_oParentBlock = $this;
	}
}

class IPCO_Builder extends IPCO_Base {
	
	private $m_oMainBlock;
	private $m_oCurrentBlock;
	private $m_aEndExpected;
	private $m_nLine;
	
	public function __construct(IPCO $ipco) {
		$this->m_oMainBlock = new IPCO_BuilderBlock('MAIN', $ipco);
		$this->m_oCurrentBlock = $this->m_oMainBlock;
	}
	
	public function onBase($sName) {
		
	}
	
	public function onIf(array $aParts) {
		array_push($this->m_aEndExpected, 'if');
		$this->m_oCurrentBlock->addToBuffer('if(){')
	}
	
	public function onElseif() {
		
	}
	
	public function onElse() {
		
	}
	
	public function onWhile() {
		
	}
	
	public function onForeach() {
		
	}
	
	public function onTemplate($sName) {
		
	}
	
	public function onComponent($sName) {
		
	}
	
	public function onBlock($sName) {
		$oBlock = new IPCO_BuilderBlock($sName);
		$this->m_oCurrentBlock->addSubBlock($oBlock);
		$this->m_oCurrentBlock = $oBlock;
		array_push($this->m_aEndExpected, "block_$sName");
	}
	
	public function onEnd($sName) {
		$sExpected = array_pop($this->m_aEndExpected);
		if($sName) {
			if($sExpected != $sName) {
				die('implement this');
			}
		}
	}
	
	private function _parseCondition($sCondition) {
		//$sCondition = Encoding::stringReplace(array('(', ')'), array(' ( ', ' ) '), $sCondition);
		// state:
		// 0: default
		// 1: string
		// 2: escaped string
		// 3: expecting braces or concatenation
		// 4: expecting numeric value or dot
		// 5: expecting numeric value
		$sResult = '';
		$nState = 0;
		for($i=0 ; $i<Encoding::length($sCondition) ; ++$i) {
			$char = Encoding::substring($sCondition, $i, 1);

			switch($nState) {
				case 0 : // default
					if($char === '\'') {
						$nState = 1;
					}
					else if(Encoding::indexOf('1234567890.', $char) !== false) {
						$nState = 4;
					}
					break;
				case 1 : // default
					if($char === '\'') {
						$nState = 3;
					}
					elseif($char === '\\') {
						$nState = 2;
					}
					break;
				case 2 : // default
					$nState = 1;
					break;
				case 3 : // default
					break;
				case 4 : // default
					if(Encoding::indexOf('1234567890', $char) !== false) {
						
					}
					else if($char === '.') {
						$nState = 5;
					}
					else if($char )
					break;
				case 5 : // default
					if(Encoding::indexOf('1234567890', $char) !== false) {
						
					}
					break;
			}
		}
	}
	
	private function _noteProblem($sMessage) {
		
	}
}

?>