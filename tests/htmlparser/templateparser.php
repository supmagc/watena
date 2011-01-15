<?php

class TemplateParser {
	
	const CHAR_TAG_OPEN 	= "<";
	const CHAR_TAG_END 		= "/";
	const CHAR_TAG_CLOSE 	= ">";
	const CHAR_EQUALS 		= "=";
	const CHAR_QUOTE_SINGLE = "'";
	const CHAR_QUOTE_DOUBLE = "\"";
	const CHAR_NEWLINE 		= "\n";
	
	const INDICATE_XML_OPEN			= "<?xml";
	const INDICATE_XML_CLOSE		= "?>";
	const INDICATE_PHP_OPEN			= "<?php";
	const INDICATE_PHP_CLOSE		= "?>";
	const INDICATE_COMMENT_OPEN		= "<!--";
	const INDICATE_COMMENT_CLOSE	= "-->";
	const INDICATE_CDATA_OPEN		= "<![CDATA[";
	const INDICATE_CDATA_CLOSE		= "]]>";
	const INDICATE_DOCTYPE_OPEN		= "!DOCTYPE";
	const INDICATE_DOCTYPE_CLOSE	= ">";
	
	const STATE_DEFAULT				=  0; // Default state
	const STATE_TAG_NAME			=  1; // When parsing the tagname
	const STATE_TAG_CONTENT			=  2; // When we found a whitespace after the tagname
	const STATE_TAG_END				=  3; // When we found as first character after start
	const STATE_TAG_SINGLE			=  4; // When we found a TAG_END within the TAG_CONTENT
	const STATE_XML					=  5;
	const STATE_PHP			 		=  6;
	const STATE_COMMENT 			=  7;
	const STATE_CDATA				=  8;
	const STATE_DOCTYPE				=  9;
	const STATE_ATTRIBUTE_NAME		= 10;
	const STATE_ATTRIBUTE_EQUALS	= 11;
	const STATE_ATTRIBUTE_SINGLE	= 12;
	const STATE_ATTRIBUTE_DOUBLE	= 13;
	
	private $m_aProblems = array();
	
	public function __construct() {
		
	}
	
	/**
	 * Parse the content of the given reader
	 * 
	 * @param TemplateReader $oReader
	 * @return TemplateBuilder
	 */
	public function parse(TemplateReader $oReader) {
		$oBuilder = new TemplateBuilder();
		
		$nState = 0;
		$nLine = 0;
		$nColumn = 0;
		$char = 0;
		
		// While characters are available, read them
		while($oReader->read()) {
			$char = $oReader->get();
			
			if($char === self::CHAR_NEWLINE) {
				++$nLine;
				$nColumn = 0;
			}
			else {
				
				++$nColumn;
				
				switch($nState) {
					case self::STATE_DEFAULT : 
						if($oReader->isStartOff(self::INDICATE_XML_OPEN)) {
							$oReader->setMark(strlen(self::INDICATE_XML_OPEN));
							$nState = self::STATE_XML;
						}
						elseif($oReader->isStartOff(self::INDICATE_PHP_OPEN)) {
							$oReader->setMark(strlen(self::INDICATE_PHP_OPEN));
							$nState = self::STATE_PHP;
						}
						elseif($oReader->isStartOff(self::INDICATE_COMMENT_OPEN)) {
							$oReader->setMark(strlen(self::INDICATE_COMMENT_OPEN));
							$nState = self::STATE_COMMENT;
						}
						elseif($oReader->isStartOff(self::INDICATE_CDATA_OPEN)) {
							$oReader->setMark(strlen(self::INDICATE_CDATA_OPEN));
							$nState = self::STATE_CDATA;
						}
						elseif($oReader->isStartOff(self::INDICATE_DOCTYPE_OPEN)) {
							$oReader->setMark(strlen(self::INDICATE_DOCTYPE_OPEN));
							$nState = self::STATE_DOCTYPE;
						}
						elseif($char === self::CHAR_TAG_OPEN) {
							$oBuilder->onContent($oReader->getMark());
							if($oReader->isFollowedBy(self::CHAR_TAG_END)) {
								$oReader->setMark(1);
								$nState = self::STATE_TAG_END;								
							}
							else {
								$oReader->setMark();
								$nState = self::STATE_TAG_NAME;
							}
						}
						break;

					case self::STATE_TAG_NAME : 
						if($this->isWhitespace($char)) {
							$oBuilder->onTagOpen($oReader->getMark());
							$nState = self::STATE_TAG_CONTENT;
						}
						else if($char === self::CHAR_TAG_END) {
							$oBuilder->onTagOpen($oReader->getMark());
							$nState = self::STATE_TAG_SINGLE;
						}
						else if($char === self::CHAR_TAG_CLOSE) {
							$oBuilder->onTagOpen($oReader->getMark());
							$oBuilder->onTagClose();
							$oReader->setMark();
							$nState = self::STATE_DEFAULT;
						}
						break;
						
					case self::STATE_TAG_CONTENT : 
						if($char === self::CHAR_TAG_CLOSE) {
							$oBuilder->onTagClose();
							$oReader->setMark();
							$nState = self::STATE_DEFAULT;
						}
						elseif($char === self::CHAR_TAG_END) {
							$nState = self::STATE_TAG_SINGLE;
						}
						elseif(!$this->isWhitespace($char)) {
							$oReader->setMark(-1);
							$nState = self::STATE_ATTRIBUTE_NAME;
						}
						break;
						
					case self::STATE_TAG_SINGLE : 
						if($char === self::CHAR_TAG_CLOSE) {
							$oBuilder->onTagSingleClose();
							$oReader->setMark();
							$nState = self::STATE_DEFAULT;
						}
						break;
						
					case self::STATE_TAG_END : 
						if($char === self::CHAR_TAG_CLOSE) {
							$oBuilder->onTagEnd($oReader->getMark());
							$oReader->setMark();
							$nState = self::STATE_DEFAULT;
						}
						break;
						
					case self::STATE_ATTRIBUTE_NAME : 
						if($char === self::CHAR_EQUALS) {
							$oBuilder->onAttributeName($oReader->getMark());
							$nState = self::STATE_ATTRIBUTE_EQUALS;
						}
						break;
						
					case self::STATE_ATTRIBUTE_EQUALS : 
						if($char === self::CHAR_QUOTE_DOUBLE) {
							$oReader->setMark();
							$nState = self::STATE_ATTRIBUTE_DOUBLE;
						}
						elseif($char === self::CHAR_QUOTE_SINGLE) {
							$oReader->setMark();
							$nState = self::STATE_ATTRIBUTE_SINGLE;
						}
						break;
						
					case self::STATE_ATTRIBUTE_DOUBLE : 
						if($char === self::CHAR_QUOTE_DOUBLE) {
							$oBuilder->onAttributeValueDouble($oReader->getMark());
							$nState = self::STATE_TAG_CONTENT;
						}
						break;
						
					case self::STATE_ATTRIBUTE_SINGLE : 
						if($char === self::CHAR_QUOTE_SINGLE) {
							$oBuilder->onAttributeValueSingle($oReader->getMark());
							$nState = self::STATE_TAG_CONTENT;
						}
						break;
					
					case self::STATE_XML : 
						if($oReader->isStartOff(self::INDICATE_XML_CLOSE)) {
							$oBuilder->onXml($oReader->getMark());
							$oReader->setMark(strlen(self::INDICATE_XML_CLOSE));
							$nState = self::STATE_DEFAULT;
						}
					
					case self::STATE_PHP : 
						if($oReader->isStartOff(self::INDICATE_PHP_CLOSE)) {
							$oBuilder->onPhp($oReader->getMark());
							$oReader->setMark(strlen(self::INDICATE_PHP_CLOSE));
							$nState = self::STATE_DEFAULT;
						}
					
					case self::STATE_COMMENT : 
						if($oReader->isStartOff(self::INDICATE_COMMENT_CLOSE)) {
							$oBuilder->onComment($oReader->getMark());
							$oReader->setMark(strlen(self::INDICATE_COMMENT_CLOSE));
							$nState = self::STATE_DEFAULT;
						}
					
					case self::STATE_CDATA : 
						if($oReader->isStartOff(self::INDICATE_CDATA_CLOSE)) {
							$oBuilder->onCData($oReader->getMark());
							$oReader->setMark(strlen(self::INDICATE_CDATA_CLOSE));
							$nState = self::STATE_DEFAULT;
						}
					
					case self::STATE_DOCTYPE : 
						if($oReader->isStartOff(self::INDICATE_DOCTYPE_CLOSE)) {
							$oBuilder->onDoctype($oReader->getMark());
							$oReader->setMark(strlen(self::INDICATE_DOCTYPE_CLOSE));
							$nState = self::STATE_DEFAULT;
						}
				}
			}
		}
		
		return $oBuilder;
	}
	
	public function noteProblem($sMessage, $nLine, $nColumn) {
		$this->m_aProblems []= array($sMessage, $nLine, $nColumn);
	}
	
	public function isWhitespace($char) {
		return in_array($char, array(" ", "\n", "\r", "\t", "\b"));
	}
}

?>