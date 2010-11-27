<?php

class Component {
	
	private $m_sContent;
	private $m_aComponents;
	private $m_aRegions;
	private $m_aVars;
	
	public function __construct($sContent) {
		$this->m_sContent = $sContent;
		
		// parse tags and stuff ... wi!i!i!i!i!i!i!
		$aMatchesBegin = array();
		$aPositionsBegin = array();
		Encoding::regFindAll('<(component|region|variable|comp|reg|var)([^<>]*?)(/)?>', $sContent, $aMatchesBegin, $aPositionsBegin);
		$nCountBegin = count($aMatchesBegin);
		$aMatchesEnd = array();
		$aPositionsEnd = array();
		Encoding::regFindAll('</(component|region|variable|comp|reg|var)>', $sContent, $aMatchesEnd, $aPositionsEnd);
		$nCountEnd = count($aMatchesEnd);
		for($i=0 ; $i<$nCountBegin ; ++$i) {
			$sTag = $aMatchesBegin[$i][1];
			$bHasEnded = $aMatchesBegin[$i][3] == '/';
			$sInnerContent = null;
			$nCountOffset = 1;
			if(!$bHasEnded) {
				$nStartOuter = $aPositionsBegin[$i][0];
				$nStartInner = $aPositionsBegin[$i][1];
				for($j=0 ; $j<$nCountEnd ; ++$j, ++$nCountOffset) {
					$nStopInner = $aPositionsEnd[$j][0];
					$nStopOuter = $aPositionsEnd[$j][1];
					
				}
			}
		}
		print_r($aMatchesBegin);
		print_r($aMatchesEnd);
	}
	
	public function __toString() {
		return $this->m_sContent;
	}
}
?>