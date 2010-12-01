<?php

class TemplateNode {
	
	public $Node;
	public $Children = array();
	public $Properties = array();
}

class Template extends Cacheable {

	private $m_oDOMDocument;
	private $m_aFields = array();
	private $m_oNodeTree;
	
	public function init() {
		$sTemplate = parent::getConfig('file', null);
		if($sTemplate) {
			$sContent = file_get_contents($sTemplate);
	
			$aMatches = array();
			$aPositions = array();
			Encoding::regFindAll('\$\{[-a-zA-Z]0-9_/]+\}', $sContent, $aMatches, $aPositions);
			$nCount = count($aMatches);
			$nOffset = 0;
			for($i=0 ; $i<$nCount ; ++$i) {
				$sHash = md5($aMatches[$i][0]);
				if(!isset($this->m_aFieldsToHashes[$sHash])) $this->m_aFieldsToHashes[$sHash] = $aMatches[$i][0];
				$sContent = Encoding::substring($sContent, 0, $aPositions[$i][0]) . $sHash . Encoding::substring($sContent, $aPositions[$i][1]);
			}
			
			$this->m_oDOMDocument = DOMDocument::loadHTML($sContent);
			$this->m_oNodeTree = new TemplateNode();
			$this->_processNode($this->m_oDOMDocument, $this->m_oNodeTree);
		}
	}
	
	public function toString() {
		return $this->m_oDOMDocument->saveHTML();
	}
	
	private function _processNode(DOMNode $oNode, TemplateNode $oTemplateNode) {
		$oTempNode = null;
		if($oNode->hasAttributes()) {
			foreach($oNode->attributes as $sAttrName => $oAttrNode) {
				if(Encoding::substring($sAttrName, 0, 4) == "tpl:") {
					if($oTempNode === null) $oTempNode = new TemplateNode();
					$oTempNode->Properties[Encoding::substring($sAttrName, 4)] = $oAttrNode->nodeValue;
				}
				
				if((($oNode->nodeName == 'a' || $oNode->nodeName == 'link') && $sAttrName == 'href') || ($oNode->nodeName == "img" && $sAttrName == "src")) {
					if(Encoding::beginsWith($oAttrNode->nodeValue, '/')) {
						$oAttrNode->nodeValue = parent::getWatena()->getMapping()->getMain() . $oAttrNode->nodeValue;
					}
				}
			}
		}
		if($oTempNode !== null) {
			$oTempNode->Node = $oNode;
			$oTemplateNode->Children []= $oTempNode;
			$oTemplateNode = $oTempNode;
		}
		if($oNode->childNodes) {
			foreach($oNode->childNodes as $oChild) {
				$this->_processNode($oChild, $oTemplateNode);
			}
		}
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
			$oTemplate = Cacheable::create('Template', array('file' => $sFile), "TL_$sTemplate", 5);
			return $oTemplate;
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