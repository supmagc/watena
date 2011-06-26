<?php

class HtmlTemplateView extends View {

	const CHAR_ELEMENT_BEGIN = '<';
	const CHAR_ELEMENT_END = '>';
	const CHAR_ELEMENT_CLOSE = '/';
	const CHAR_ATTRIBUTE_EQUALITY = '=';
	const CHAR_ATTRIBUTE_QUOTE_DOUBLE = '=';
	const CHAR_ATTRIBUTE_QUOTE_SINGLE = '\'';
	const CHAR_ATTRIBUTE_ESCAPE = '\\';
	const CHAR_DOCTYPE = '!';
	
	const STATE_NORMAL = 1;
	const STATE_ELEMENT_NAME = 2;
	const STATE_ELEMENT_ATTRIBUTES = 3;
	const STATE_ELEMENT_CLOSURE = 4;
	const STATE_ATTRIBUTE_NAME = 5;
	const STATE_ATTRIBUTE_QUOTE = 6;
	const STATE_ATTRIBUTE_VALUE = 7;
	const STATE_ATTRIBUTE_VALUE_ESCAPED = 7;
	const STATE_DOCTYPE = 8;
	
	public function render(Model $oModel) {
		$oPlugin = parent::getWatena()->getContext()->getPlugin('TemplateLoader');
		$oTemplate = $oPlugin->load(parent::getConfig('template', 'index.tpl'), array(array('HtmlTemplateView', 'searchTemplateContent')));
		echo "" . $oTemplate->createTemplateClass();
	}
	
	public static function getRequirements() {
		return array('plugins' => 'TemplateLoader');
	}
	
	public static function searchTemplateContent($sContent) {
		$nLength = Encoding::length($sContent);
		$nState = self::STATE_NORMAL;
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
						$sNextChar = Encoding::substring($sContent, ++$i, 1);
						if($sNextChar === self::CHAR_DOCTYPE) {
							$nState = self::STATE_DOCTYPE;
						}
						else if($sNextChar === self::CHAR_ELEMENT_CLOSE) {
							$nState = self::STATE_ELEMENT_CLOSURE;
						}
						else if(self::isHtmlNameCharacter($sNextChar)) {
							$nState = self::STATE_ELEMENT_NAME;
							$nMarker = $i;
						}
					}
					break;
					
				case self::STATE_ELEMENT_NAME:
					if(!self::isHtmlNameCharacter($sChar)) {
						if($sChar === self::CHAR_ELEMENT_CLOSE)
							$nState = self::STATE_ELEMENT_CLOSURE;
						else if($sChar === self::CHAR_ELEMENT_END)
							$nState = self::STATE_ELEMENT_NORMAL;
						else if(is_whitespace($sChar))
							$nState = self::STATE_ELEMENT_ATTRIBUTES;
						$sElement = Encoding::substring($sContent, $nMarker, $i - $nMarker);
					}
					break;
					
				case self::STATE_ELEMENT_ATTRIBUTES:
					if($sChar === self::CHAR_ELEMENT_CLOSE) {
						$nState = self::STATE_ELEMENT_CLOSURE;
					}
					else if($sChar === self::CHAR_ELEMENT_END) {
						$nState = self::STATE_ELEMENT_NORMAL;
					}
					else if(self::isHtmlNameCharacter($sChar)) {
						$nState = self::STATE_ATTRIBUTE_NAME;
						$nMarker = $i;
					}
					break;
					
				case self::STATE_ATTRIBUTE_NAME:
					if($sChar === self::CHAR_ATTRIBUTE_EQUALITY) {
						$nState = self::STATE_ATTRIBUTE_QUOTE;
						$sAttribute = Encoding::substring($sContent, $nMarker, $i - $nMarker);
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
						$sValue = Encoding::substring($sContent, $nMarker, $i - $nMarker - 1);
						echo "$sElement ($sAttribute = $sValue) \n";
					}
					break;
					
				case self::STATE_ATTRIBUTE_VALUE_ESCAPED:
					$nState = self::STATE_ATTRIBUTE_VALUE;
					break;
					
				case self::STATE_ELEMENT_CLOSURE:
					if($sChar === self::CHAR_ELEMENT_END)
						$nState = self::STATE_NORMAL;
					break;
					
				case self::STATE_DOCTYPE:
					if($sChar === self::CHAR_ELEMENT_END)
						$nState = self::STATE_NORMAL;
					break;
			}
		}
		return array();
	}
	
	public static function isHtmlNameCharacter($sChar) {
		return is_alphabetical($sChar) || $sChar === '-' || $sChar === ':';
	}
}

?>