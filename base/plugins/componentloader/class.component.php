<?php

class Component extends Cacheable {
	
	private $m_sContent;
	private $m_aComponents;
	private $m_aRegions;
	private $m_aVars;
	
	public function __construct($sContent) {	
		
		$oTest = new DOMDocument('1.0', 'UTF-8');
		$oTest->loadHTML($sContent);
		
		
		$aMatches = array();
		$aPositions = array();
		while(Encoding::regFind('<(component|variable|comp|var)([^<>]*?)(/)?>', $sContent, $aMatches, $aPositions)) {
			$sTag = $aMatches[1];
			$nOuterStart = $aPositions[0];
			$nOuterStop = $aPositions[1];
			$sInnerContent = null;
			$nInnerStart = 0;
			$nInnerStop = 0;
			
			// If we have inner content, search for it
			if($aMatches[3] != '/') {
				$sShortContent = Encoding::substring($sContent, $aPositions[1]);
				$nShortOffset = $aPositions[1];
				$aShortMatches = array();
				$aShortPositions = array();
				Encoding::regFindAll('<(/)?(component|variable|comp|var)([^<>]*?)(/)?>', $sShortContent, $aShortMatches, $aShortPositions);
				$nSkipCount = 0;

				for($i=0 ; $i<count($aShortMatches) ; ++$i) {
					$sShortTag = $aShortMatches[$i][2];
					if($sShortTag == $sTag) {
						if($aShortMatches[$i][1] === '/') {
							if($nSkipCount === 0) {
								$nOuterStart = $aPositions[0];
								$nOuterStop = $nShortOffset + $aShortPositions[$i][1];
								$nInnerStart = $aPositions[1];
								$nInnerStop = $nShortOffset + $aShortPositions[$i][0];
								$sInnerContent = Encoding::substring($sContent, $nInnerStart, $nInnerStop - $nInnerStart);
								break;
							}
							--$nSkipCount;
						}
						else if($aShortMatches[$i][4] !== '/') {
							++$nSkipCount;
						}
					}
				}
			}
			$sContent = Encoding::substring($sContent, 0, $nOuterStart) . Encoding::substring($sContent, $nOuterStop);

			
		}
		$this->m_sContent = $sContent;
	}
	
	public function __toString() {
		return $this->m_sContent;
	}
}
?>