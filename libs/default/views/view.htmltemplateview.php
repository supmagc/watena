<?php
require_plugin('TemplateLoader');
require_includeonce(dirname(__FILE__) . '/../ipco/ipco.php');

class HtmlTemplateView extends View implements IPCO_IContentParser {

	const CHAR_ELEMENT_BEGIN = '<';
	const CHAR_ELEMENT_END = '>';
	const CHAR_ELEMENT_CLOSE = '/';
	const CHAR_ATTRIBUTE_EQUALITY = '=';
	const CHAR_ATTRIBUTE_QUOTE_DOUBLE = '"';
	const CHAR_ATTRIBUTE_QUOTE_SINGLE = '\'';
	const CHAR_ATTRIBUTE_ESCAPE = '\\';
	const CHAR_DOCTYPE_BEGIN = '<!';
	const CHAR_COMMENT_BEGIN = '<!--';
	const CHAR_CDATA_BEGIN = '<![CDATA[';
	const CHAR_DOCTYPE_END = '>';
	const CHAR_COMMENT_END = '-->';
	const CHAR_CDATA_END = ']]>';
	
	const STATE_NORMAL = 1;
	const STATE_ELEMENT_NAME = 2;
	const STATE_ELEMENT_ATTRIBUTES = 3;
	const STATE_ELEMENT_CLOSURE = 4;
	const STATE_ATTRIBUTE_NAME = 5;
	const STATE_ATTRIBUTE_QUOTE = 6;
	const STATE_ATTRIBUTE_VALUE = 7;
	const STATE_ATTRIBUTE_VALUE_ESCAPED = 7;
	const STATE_DOCTYPE = 8;

	private $m_oHtmlModel;
	
	private $m_sHeadFilter = 'head';
	private $m_aLinkFilters = array(
		'a' => 'href', 
		'link' => 'href',
		'img' => 'src',
		'form' => 'action',
		'iframe' => 'src',
		'script' => 'src'
	);
	
	public function headers(Model $oModel = null) {
		$this->headerContentType(is_a($oModel, 'HtmlModel') ? $oModel->getContentType() : 'text/html', is_a($oModel, 'HtmlModel') ? $oModel->getCharset() : Encoding::charset());
	}
	
	public function render(Model $oModel = null) {
		if(is_a($oModel, 'HtmlModel')) $this->m_oHtmlModel = $oModel;
		$oPlugin = parent::getWatena()->getContext()->getPlugin('TemplateLoader');
		$oGenerator = $oPlugin->load(parent::getConfig('template', 'index.tpl'), $this);
		$oGenerator->componentPush($oModel);
		echo $oGenerator->getContent(true);
	}
	
	public function addMappingRoot($sElement, $sAttribute, $sValue) {
		// TODO: discover files !!
		return parent::getWatena()->getMapping()->getRoot() . $sValue;
	}
	
	public function addHead() {
		return $this->m_oHtmlModel ? $this->m_oHtmlModel->getHead() : '';
	}
	
	public function parseContent(&$sContent) {
		$nLength = Encoding::length($sContent);
		$nState = self::STATE_NORMAL;
		$aParts = array();
		$nMarker = 0;
		$sElement = '';
		$sAttribute = '';
		$sValue = '';
		$sQuote = '';
		for($i=0 ; $i<$nLength ; ++$i) {
			$sChar = Encoding::substring($sContent, $i, 1);
			switch($nState) {
				case self::STATE_NORMAL:
					if($sChar === self::CHAR_ELEMENT_BEGIN) {
						$nIndex = $i;
						$nIndex = self::_findAndGoPassed($sContent, $nIndex, self::CHAR_CDATA_BEGIN, self::CHAR_CDATA_END);
						$nIndex = self::_findAndGoPassed($sContent, $nIndex, self::CHAR_COMMENT_BEGIN, self::CHAR_COMMENT_END);
						$nIndex = self::_findAndGoPassed($sContent, $nIndex, self::CHAR_DOCTYPE_BEGIN, self::CHAR_DOCTYPE_END);
						if($nIndex !== $i) 
							$i = $nIndex;
						else {
							$sNextChar = Encoding::substring($sContent, ++$i, 1);
							if($sNextChar === self::CHAR_ELEMENT_CLOSE) {
								$nState = self::STATE_ELEMENT_CLOSURE;
								$nMarker = $i + 1;
							}
							else if($this->_isHtmlNameCharacter($sNextChar)) {
								$nState = self::STATE_ELEMENT_NAME;
								$nMarker = $i;
							}							
						}
					}
					break;
					
				case self::STATE_ELEMENT_NAME:
					if(!$this->_isHtmlNameCharacter($sChar)) {
						if($sChar === self::CHAR_ELEMENT_CLOSE)
							$nState = self::STATE_ELEMENT_CLOSURE;
						else if($sChar === self::CHAR_ELEMENT_END)
							$nState = self::STATE_NORMAL;
						else if(is_whitespace($sChar))
							$nState = self::STATE_ELEMENT_ATTRIBUTES;
						$sElement = Encoding::toLower(Encoding::substring($sContent, $nMarker, $i - $nMarker));
					}
					break;
					
				case self::STATE_ELEMENT_ATTRIBUTES:
					if($sChar === self::CHAR_ELEMENT_CLOSE)
						$nState = self::STATE_ELEMENT_CLOSURE;
					else if($sChar === self::CHAR_ELEMENT_END)
						$nState = self::STATE_NORMAL;
					else if($this->_isHtmlNameCharacter($sChar)) {
						$nState = self::STATE_ATTRIBUTE_NAME;
						$nMarker = $i;
					}
					break;
					
				case self::STATE_ATTRIBUTE_NAME:
					if(!$this->_isHtmlNameCharacter($sChar)) {
						$nState = self::STATE_ATTRIBUTE_QUOTE;
						$sAttribute = Encoding::toLower(Encoding::substring($sContent, $nMarker, $i - $nMarker));
					}
					break;
					
				case self::STATE_ATTRIBUTE_QUOTE:
					if($sChar === self::CHAR_ATTRIBUTE_QUOTE_DOUBLE) {
						$nState = self::STATE_ATTRIBUTE_VALUE;
						$nMarker = $i + 1;
						$sQuote = self::CHAR_ATTRIBUTE_QUOTE_DOUBLE;
					}
					else if($sChar === self::CHAR_ATTRIBUTE_QUOTE_SINGLE) {
						$nState = self::STATE_ATTRIBUTE_VALUE;
						$nMarker = $i + 1;
						$sQuote = self::CHAR_ATTRIBUTE_QUOTE_SINGLE;
					}
					break;
					
				case self::STATE_ATTRIBUTE_VALUE:
					if($sChar === self::CHAR_ATTRIBUTE_ESCAPE) {
						$nState = self::STATE_ATTRIBUTE_VALUE_ESCAPED;
					}
					else if($sChar === $sQuote) {
						$nState = self::STATE_ELEMENT_ATTRIBUTES;
						$sValue = Encoding::substring($sContent, $nMarker, $i - $nMarker);
						if(isset($this->m_aLinkFilters[$sElement]) && $this->m_aLinkFilters[$sElement] == $sAttribute && Encoding::beginsWith($sValue, '/')) {
							$aParts []= new IPCO_ContentParserPart($nMarker, $i - $nMarker, 'addMappingRoot', array($sElement, $sAttribute, $sValue));
						}
					}
					break;
					
				case self::STATE_ATTRIBUTE_VALUE_ESCAPED:
					$nState = self::STATE_ATTRIBUTE_VALUE;
					break;
					
				case self::STATE_ELEMENT_CLOSURE:
					if($sChar === self::CHAR_ELEMENT_END) {
						$sElement = Encoding::toLower(Encoding::trim(Encoding::substring($sContent, $nMarker, $i - $nMarker)));
						if($sElement == $this->m_sHeadFilter) {
							$aParts []= new IPCO_ContentParserPart($nMarker - 2, 0, 'addHead');
						}
						$nState = self::STATE_NORMAL;
					}
					break;
					
				case self::STATE_DOCTYPE:
					if($sChar === self::CHAR_ELEMENT_END)
						$nState = self::STATE_NORMAL;
					break;
			}
		}
		return $aParts;
	}
	
	private function _isHtmlNameCharacter($sChar) {
		return is_alphabetical($sChar) || $sChar === '-' || $sChar === ':';
	}
	
	private function _findAndGoPassed(&$sContent, $nIndex, $sBegin, $sEnd) {
		if(Encoding::substring($sContent, $nIndex, Encoding::length($sBegin)) == $sBegin) {
			$nTemp = Encoding::indexOf($sContent, $sEnd, $nIndex + Encoding::length($sBegin));
			if($nTemp !== false) {
				return $nTemp + Encoding::length($sEnd);
			}
		}
		return $nIndex;
	}
}

?>