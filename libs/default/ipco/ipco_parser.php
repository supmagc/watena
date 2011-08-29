<?php

class IPCO_Parser extends IPCO_Base {
	
	const STATE_DEFAULT 	= 0;
	const STATE_IPCO 		= 1;
	const STATE_IPCO_QUOTE 	= 3;
	const STATE_IPCO_VAR 	= 4;
	const STATE_IPCO_BQUOTE	= 5;
	const REGION_MAIN		= '__MainRegion__';
	
	private $m_sIdentifier;
	private $m_sClassName;
	private $m_sContent;
	private $m_nDepth;
	private $m_aEndings;
	private $m_oContentParser = null;
	private $m_sExtendsTemplate = null;
	private $m_sExtendsFilePath = null;
	private $m_aActiveBuffer = null;
	private $m_aRegionBuffers = null;
	private $m_sCurrentRegion = null;
	private $m_aRegions = array();
	private $m_oRegion = null;
	
	public function __construct($sIdentifier, &$sContent, IPCO $ipco) {
		parent::__construct($ipco);
		$this->m_sIdentifier = $sIdentifier;
		$this->m_sClassName = parent::getIpco()->getClassName($sIdentifier);
		$this->m_sContent = $sContent;
	}
	
	public function setContentParser(IPCO_IContentParser $oContentParser) {
		$this->m_oContentParser = $oContentParser;
	}
	
	public function getIdentifier() {
		return $this->m_sIdentifier;
	}
	
	public function getClassName() {
		return $this->m_sClassName;
	}
	
	public function getExtendsTemplate() {
		return $this->m_sExtendsTemplate;
	}
	
	public function getExtendsFilePath() {
		return $this->m_sExtendsFilePath;
	}
	
	public function parse() {
		$this->m_nDepth = 0;
		$this->m_aEndings = array();
		$this->m_sExtends = null;
		$this->m_aRegions = array();
		$this->m_oRegion = new IPCO_ParserRegion(self::REGION_MAIN, parent::getIpco());
		$this->m_aRegions[$this->m_oRegion->getName()] = $this->m_oRegion;
		$nMark = 0;
		$aBuffer = array(); //IPCO_ParserSettings::getPageHeader($this->m_sClassName, 'IPCO_Processor'));
		$nState = self::STATE_DEFAULT;
		$nLength = Encoding::length($this->m_sContent);
 		
		for($i=0 ; $i<$nLength ; ++$i) {
			
			$char1 = Encoding::substring($this->m_sContent, $i, 1);
			$char2 = Encoding::substring($this->m_sContent, $i, 2);
			
			switch($nState) {
				case self::STATE_DEFAULT : 
					if($char2 === '{%') {
						$this->interpretContent(Encoding::substring($this->m_sContent, $nMark, $i-$nMark));
						$nMark = $i += 2;
						$nState = self::STATE_IPCO;
					}
					else if($char2 === '{[') {
						$this->interpretContent(Encoding::substring($this->m_sContent, $nMark, $i-$nMark));
						$nMark = $i += 2;
						$nState = self::STATE_IPCO_VAR;
					}
					break;
					
				case self::STATE_IPCO : 
					if($char2 === '%}') {
						$this->interpretFilter(Encoding::substring($this->m_sContent, $nMark, $i-$nMark));
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
						$this->interpretVariable(Encoding::substring($this->m_sContent, $nMark, $i-$nMark));
						$nMark = $i += 2;
						$nState = self::STATE_DEFAULT;
					}
					break;
					
				case self::STATE_IPCO_BQUOTE : 
					$nState = self::STATE_IPCO_QUOTE;
					break;
			}
		}
		$this->interpretContent(Encoding::substring($this->m_sContent, $nMark, $nLength-$nMark));
		
		$aBuffer = array();
		$aBuffer []= IPCO_ParserSettings::getPageHeader($this->m_sClassName, $this->m_sExtendsFilePath ? parent::getIpco()->getClassName($this->m_sExtendsFilePath) : 'IPCO_Processor');
		if($this->m_oRegion->hasContent())
			$aBuffer []= IPCO_ParserSettings::getPageGenerator(self::REGION_MAIN);
		foreach($this->m_aRegions as $oRegion) {
			$aBuffer []= $oRegion->build();
		}
		$aBuffer []= IPCO_ParserSettings::getPageFooter();
		
		return implode('', $aBuffer);
	}
	
	public function interpretContent($sContent) {
		if(Encoding::length($sContent) > 0) {
			$aReturn = array();
			$aContentParserParts = $this->m_oContentParser->parseContent($sContent);
			$nOffset = 0;
			if(is_array($aContentParserParts)) {
				foreach($aContentParserParts as $oContentParserPart) {
					$aReturn []= $this->getDepthOffset() . IPCO_ParserSettings::getContent(
						Encoding::substring($sContent, $nOffset, $oContentParserPart->getStart() - $nOffset)
					);
					$aReturn []= $this->getDepthOffset() . IPCO_ParserSettings::getContentParserPart(
						$oContentParserPart->getMethod(),
						$oContentParserPart->getParams()
					);
					$nOffset = $oContentParserPart->getStart() + $oContentParserPart->getLength();
				}
			}
			$aReturn []= $this->getDepthOffset() . IPCO_ParserSettings::getContent(Encoding::substring($sContent, $nOffset));
			$this->m_oRegion->addLines($aReturn);
		}
	}
	
	private function interpretFilter($sContent) {
		$aParts = array_map(array('Encoding', 'trim'), explode(' ', Encoding::trim($sContent)));
		$sName = array_shift($aParts);
		switch($sName) {
			case 'if' : $this->interpretIf(new IPCO_Expression(implode(' ', $aParts), parent::getIpco())); break;
			case 'foreach' : $this->interpretForeach(new IPCO_Expression(implode(' ', $aParts), parent::getIpco())); break;
			case 'while' : $this->interpretWhile(new IPCO_Expression(implode(' ', $aParts), parent::getIpco())); break;
			case 'else' : $this->interpretElse(); break;
			case 'elseif' : $this->interpretElseIf(new IPCO_Expression(implode(' ', $aParts), parent::getIpco())); break;
			case 'end' : $this->interpretEnd(count($aParts) > 0 ? $aParts[0] : null); break;
			case 'extends' : $this->interpretExtends(count($aParts) > 0 ? $aParts[0] : null); break;
			case 'include' : $this->interpretInclude(count($aParts) > 0 ? $aParts[0] : null); break;
			case 'region' : $this->interpretRegion($aParts); break;
		}
	}
	
	public function interpretVariable($sContent) {
		$oCondition = new IPCO_Expression($sContent, parent::getIpco());
		$this->m_oRegion->addLine($this->getDepthOffset() . IPCO_ParserSettings::getVariable($oCondition->__toString()));
	}
	
	public function interpretIf(IPCO_Expression $oCondition) {
		array_push($this->m_aEndings, 'if');
		$this->m_oRegion->addLine($this->getDepthOffset(0, 1) . IPCO_ParserSettings::getFilterIf($oCondition->__toString()));
	}
	
	public function interpretElseIf(IPCO_Expression $oCondition) {
		$this->m_oRegion->addLine($this->getDepthOffset(-1, 1) . IPCO_ParserSettings::getFilterElseIf($oCondition->__toString()));
	}
	
	public function interpretElse() {
		$this->m_oRegion->addLine($this->getDepthOffset(-1, 1) . IPCO_ParserSettings::getFilterElse());
	}
	
	public function interpretForeach(IPCO_Expression $oCondition) {
		array_push($this->m_aEndings, 'foreach');
		$this->m_oRegion->addLine($this->getDepthOffset(0, 1) . IPCO_ParserSettings::getFilterForeach($oCondition->__toString()));
	}
	
	public function interpretWhile(IPCO_Expression $oCondition) {
		array_push($this->m_aEndings, 'while');
		$this->m_oRegion->addLine($this->getDepthOffset(0, 1) . IPCO_ParserSettings::getFilterWhile($oCondition->__toString()));
	}
	
	public function interpretEnd($aParts) {
		$sExpectedEnding = array_pop($this->m_aEndings);
		$sGivenEnding = Encoding::toLower(Encoding::trim($aParts));
		if(Encoding::length($sGivenEnding) > 0 && $sExpectedEnding != $sGivenEnding) {
			throw new IPCO_Exception('Invalid IPCO-tag nesting.', IPCO_Exception::INVALIDNESTING);
		}
		switch($sExpectedEnding) {
			case 'if' : $this->m_oRegion->addLine($this->getDepthOffset(-1, 0) . IPCO_ParserSettings::getFilterEndIf()); break;
			case 'foreach' : $this->m_oRegion->addLine($this->getDepthOffset(-1, 0) . IPCO_ParserSettings::getFilterEndForeach()); break;
			case 'while' : $this->m_oRegion->addLine($this->getDepthOffset(-1, 0) . IPCO_ParserSettings::getFilterEndWhile()); break;
		}
	}

	public function interpretExtends($sName = null) {
		$sName = Encoding::trim($sName);
		$this->m_sExtendsTemplate = Encoding::length($sName) > 0 ? $sName : null;
		$this->m_sExtendsFilePath = parent::getIpco()->getFileFromTemplate($this->m_sExtendsTemplate);
		if(!file_exists($this->m_sExtendsFilePath) || !is_readable($this->m_sExtendsFilePath))
			throw new IPCO_Exception(IPCO_Exception::FILTER_EXTENDS_INVALID_FILE);
	}
	
	public function interpretInclude($sName = null) {
		
	}
	
	public function interpretRegion(array $aParts) {
		if(count($aParts) > 0) {
			$sOperator = $aParts[0];
			$sName = count($aParts) > 1 ? $aParts[1] : null; 
			if($sOperator === 'end' || $sOperator === 'close' || $sOperator === 'stop') {
				if($sName !== null && $sName !== $this->m_oRegion->getName())
					throw new IPCO_Exception(IPCO_Exception::FILTER_REGION_END_CURRENT_MISMATCH);
				if($this->m_oRegion->getName() === self::REGION_MAIN)
					throw new IPCO_Exception(IPCO_Exception::FILTER_REGION_END_NONE_CURRENT);
				$this->m_oRegion = $this->m_aRegions[self::REGION_MAIN];
			}
			else if($sOperator === 'begin' || $sOperator === 'open' || $sOperator === 'start') {
				if($this->m_oRegion->getName() !== self::REGION_MAIN)
					throw new IPCO_Exception(IPCO_Exception::FILTER_REGION_BEGIN_HAS_CURRENT);
				if($sName === null)
					throw new IPCO_Exception(IPCO_Exception::FILTER_REGION_NO_NAME);
				$this->m_oRegion = new IPCO_ParserRegion($sName, parent::getIpco());
				$this->m_aRegions[$this->m_oRegion->getName()] = $this->m_oRegion;
			}
			else if($sOperator === 'use' || $sOperator === 'include') {
				if($sName === null)
					throw new IPCO_Exception(IPCO_Exception::FILTER_REGION_NO_NAME);
				$this->m_oRegion->addLine(IPCO_ParserSettings::getCallRegion($sName));
			}
		}
		else {
			throw new IPCO_Exception(IPCO_Exception::FILTER_REGION_NO_TAG);
		}
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