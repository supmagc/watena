<?php

class Template extends Cacheable {

	private $m_sContent;
	
	public function init() {
		$sTemplate = parent::getConfig('file', null);
		if($sTemplate) {
			$sContent = file_get_contents($sTemplate);
			$oTest = DOMDocument::loadHTML($sContent);
			
			$this->_processNode($oTest);
	
			$aMatches = array();
			$aPositions = array();
			$sContent = $oTest->saveHTML();
			Encoding::regFindAll('\$\{[-a-zA-Z]0-9_/]+\}', $sContent, $aMatches, $aPositions);
			$nCount = count($aMatches);
			$nOffset = 0;
			for($i=0 ; $i<$nCount ; ++$i) {
				
			}
			
			$this->m_sContent = $sContent;			
		}
	}
	
	private function _processNode(DOMNode $oNode) {
		//echo $oNode->nodeName . "\n";
		if($oNode->hasAttributes()) {
			foreach($oNode->attributes as $sAttrName => $oAttrNode) {
				if(Encoding::substring($sAttrName, 0, 4) == "tpl:") {
					$sAttrName = Encoding::substring($sAttrName, 4);
					switch($sAttrName) {
						case 'content' : break;
						case 'replace' : break;
						case 'repeat' : break;
						case 'component' : break;
						case 'condition' : break;
						case 'enabled' : break;
					}
				}
				
				if((($oNode->nodeName == 'a' || $oNode->nodeName == 'link') && $sAttrName == 'href') || ($oNode->nodeName == "img" && $sAttrName == "src")) {
					if(Encoding::beginsWith($oAttrNode->nodeValue, '/')) {
						$oAttrNode->nodeValue = parent::getWatena()->getMapping()->getMain() . $oAttrNode->nodeValue;
					}
				}
			}
		}
		if($oNode->childNodes) {
			foreach($oNode->childNodes as $oChild) {
				$this->_processNode($oChild);
			}
		}
	}
	
	public function toString() {
		return $this->m_sContent;
	}
}

class TemplateLoader extends Plugin {

	private $m_sDirectory;
	private $m_sExtension;
	
	public function init() {
		$this->m_sDirectory = parent::getWatena()->getPath(parent::getConfig('DIRECTORY', 'D:templates'));
		$this->m_sExtension = parent::getConfig('EXTENSION', 'tpl');
	}
	
	public function load($sTemplate) {
		if($this->m_sDirectory && $this->m_sExtension) {
			$sFile = $this->m_sDirectory . '/' . $sTemplate . '.' . $this->m_sExtension;
			return Cacheable::create('Template', array('file' => $sFile), "TL_$sTemplate", 5);
		}
	}
		
	/**
	 * Retrieve version information of this plugin.
	 * The format is an associative array as follows:
	 * 'major' => Major version (int)
	 * 'minor' => Minor version (int)
	 * 'build' => Build version (int)
	 * 'state' => Naming of the production state
	 */
	public function getVersion() {
		return array('major' => 0, 'minor' => 1, 'build' => 1, 'state' > 'dev');
	}
}

?>