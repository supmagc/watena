<?php

class TemplateParser {
	
	const CHAR_TAG_OPEN = "<";
	const CHAR_TAG_END = "/";
	const CHAR_TAG_CLOSE = ">";
	const CHAR_ATTRIBUTE = "=";
	const CHAR_QUOTE_SINGLE = "'";
	const CHAR_QUOTE_DOUBLE = "\"";
	const CHAR_WHITESPACE = " ";
	const CHAR_NEWLINE = "\n";
	
	const INDICATE_CLOSE 			= "/";
	const INDICATE_XML				= "?xml";
	const INDICATE_PREPROCESSOR		= "?";
	const INDICATE_COMMENT			= "!--";
	const INDICATE_CDATA			= "![CDATA[";
	const INDICATE_DOCTYPE			= "!DOCTYPE";
	
	const STATE_DEFAULT				=  0; // Default state
	const STATE_TAG_NAME			=  1; // When parsing the tagname
	const STATE_TAG_CONTENT			=  2; // When we found a whitespace after the tagname
	const STATE_TAG_END				=  3; // When we found as first character after start
	const STATE_TAG_SINGLE			=  4; // When we found a TAG_END within the TAG_CONTENT
	const STATE_XML					=  5;
	const STATE_PREPROCESSOR 		=  6;
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
						if($char === self::CHAR_TAG_OPEN && $oReader->isFollowedBy(self::INDICATE_XML)) {
							$nState = self::STATE_DOCTYPE;
							$oReader->setMark(strlen(self::INDICATE_XML));
						}
						elseif($char === self::CHAR_TAG_OPEN && $oReader->isFollowedBy(self::INDICATE_PREPROCESSOR)) {
							$nState = self::STATE_PREPROCESSOR;
							$oReader->setMark(strlen(self::INDICATE_PREPROCESSOR));
						}
						elseif($char === self::CHAR_TAG_OPEN && $oReader->isFollowedBy(self::INDICATE_COMMENT)) {
							$nState = self::STATE_COMMENT;
							$oReader->setMark(strlen(self::INDICATE_COMMENT));
						}
						elseif($char === self::CHAR_TAG_OPEN && $oReader->isFollowedBy(self::INDICATE_CDATA)) {
							$nState = self::STATE_CDATA;
							$oReader->setMark(strlen(self::INDICATE_CDATA));
						}
						elseif($char === self::CHAR_TAG_OPEN && $oReader->isFollowedBy(self::INDICATE_DOCTYPE)) {
							$nState = self::STATE_DOCTYPE;
							$oReader->setMark(strlen(self::INDICATE_DOCTYPE));
						}
						elseif($char === self::CHAR_TAG_OPEN && $oReader->isFollowedBy(self::CHAR_TAG_END)) {
							$nState = self::STATE_TAG_END;
							$oReader->setMark(strlen(self::CHAR_TAG_END));
						}
						elseif($char === self::CHAR_TAG_OPEN) {
							$nState = self::STATE_TAG_NAME;
							$oReader->setMark();
						}
						break;

					case self::STATE_TAG_NAME : 
						if($char === self::CHAR_WHITESPACE) {
							$oBuilder->onTagOpen($oReader->getMark());
							$nState = self::STATE_TAG_CONTENT;
						}
						else if($char === self::CHAR_TAG_END) {
							$oBuilder->onTagOpen($oReader->getMark());
							$nState = self::STATE_TAG_SINGLE;
						}
						else if($char === self::CHAR_TAG_CLOSE) {
							$oBuilder->onTagOpen($oReader->getMark());
							$oBuilder->onTagClose(false);
							$nState = self::STATE_DEFAULT;
						}
						break;
						
					case self::STATE_TAG_CONTENT : 
						if($char === self::CHAR_TAG_CLOSE) {
							$oBuilder->onTagClose(false);
							$nState = self::STATE_DEFAULT;
						}
						else {
							// Todo process tag content
						}
						break;
						
					case self::STATE_TAG_SINGLE : 
						if($char === self::CHAR_TAG_CLOSE) {
							$nState = self::STATE_DEFAULT;
							$oBuilder->onTagClose(true);
						}
						break;
						
					case self::STATE_TAG_END : 
						if($char === self::CHAR_TAG_CLOSE) {
							$nState = self::STATE_DEFAULT;
							$oBuilder->onTagEnd($oReader->getMark());
						}
						break;
				}
			}
		}
		
		return $oBuilder;
	}
	
	public function noteProblem($sMessage, $nLine, $nColumn) {
		$this->m_aProblems []= array($sMessage, $nLine, $nColumn);
	}
}

?>