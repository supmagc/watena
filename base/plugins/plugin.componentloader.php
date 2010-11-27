<?php

require_once 'componentloader/class.component.php';

class ComponentLoader extends Plugin {

	private $m_sPathTemplates;
	private $m_sPathCode;
	private $m_sExtension;
	
	public function init() {
		$this->m_sPathTemplates = parent::getWatena()->getPath(parent::getConfig('DIR_TEMPLATE', 'components'));
		$this->m_sPathCode = parent::getWatena()->getPath(parent::getConfig('DIR_CODE', 'components'));
		$this->m_sExtension = parent::getConfig('EXTENSION', '');
	}

	public function getVersion() {
		return array('major' => 0, 'minor' => 1, 'build' => 1, 'state' => 'dev');
	}
	
	public function load($sComponent) {
		$sContent = file_get_contents(realpath($this->m_sPathTemplates . '/' . $sComponent . '.' . $this->m_sExtension));
		$oTest = DOMDocument::loadHTML($sContent);
		
		$this->_processNode($oTest);

		$aMatches = array();
		$aPositions = array();
		$sContent = Encoding::regFindAll('\$\{[-a-zA-Z]0-9_/]+\}', $sContent, $aMatches, $aPositions);
		$nCount = count($aMatches);
		$nOffset = 0;
		for($i=0 ; $i<$nCount ; ++$i) {
			
		}
		//return parent::getWatena()->getCache()->retrieve('CL_'.$sComponent, array($this, '_loadComponentFromFile'), 5, array($sComponent));
	}
	
	public function _processNode(DOMNode $oNode) {
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
	
	public function _loadComponentFromFile($sComponent) {
		$sContent = file_get_contents(realpath($this->m_sPathTemplates . '/' . $sComponent . '.' . $this->m_sExtension));
		return new Component($sContent);
	}
}

?>