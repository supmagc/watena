<?php

class Template extends Cacheable {

	private $m_sContent;
	
	public function wakeup() {
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
			
			$oDOM = DOMDocument::loadHTML($sContent);
			$this->_processNode($oDOM);
			$this->m_sContent = $oDOM->saveHTML();
		}
	}
	
	public function toString() {
		return $this->m_sContent;
	}
	
	private function _processNode(DOMNode $oNode) {
		if($oNode->nodeType == XML_ELEMENT_NODE) {
			$oElement = $oNode;
			if($oElement->hasAttributes()) {
				$aAttributes = array();
				foreach($oElement->attributes as $sAttrName => $oAttrNode) {
					if(Encoding::substring($sAttrName, 0, 4) == "tpl:") {
						$aAttributes[Encoding::substring($sAttrName, 4)] = $oAttrNode->nodeValue;
					}				
					if((($oElement->nodeName == 'a' || $oElement->nodeName == 'link') && $sAttrName == 'href') || 
						($oElement->nodeName == "img" && $sAttrName == "src")) {
						if(Encoding::beginsWith($oAttrNode->nodeValue, '/')) {
							$oAttrNode->nodeValue = parent::getWatena()->getMapping()->getMain() . $oAttrNode->nodeValue;
						}
					}
				}
				
				if(!isset($aAttributes['enabled']) || $aAttributes['enabled']) {
					$sContent = null;
					if(isset($aAttributes['file'])) {
						$oTemplateLoader = parent::getWatena()->getContext()->getPlugin('TemplateLoader');
						$sContent = $oTemplateLoader->load($aAttributes['file'])->toString();
					}
					else if(isset($aAttributes['content'])) {
						$sContent = $aAttributes['content'];
						$sContent = '<strong tpl:test="bleuh">bla</strong>';
					}
					if($sContent !== null) {
						//$oFragment = $oElement->ownerDocument->createDocumentFragment();
						//$oFragment->appendXML($sContent);
						$oFragment = new DOMDocument('1.0', 'UTF-8');
						$oFragment->loadHTML("<html>$sContent</html>");
						$oFragment = $oNode->ownerDocument->importNode($oFragment->getElementsByTagName('html')->item(0));
						
						$sMode = 'insert';
						if(isset($aAttributes['mode'])) $sMode = $aAttributes['mode'];
						$oElement->appendChild($oFragment);
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