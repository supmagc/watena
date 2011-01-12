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
	
	const STATE_DEFAULT				=  0;
	const STATE_TAG_NAME			=  6;
	const STATE_TAG_CONTENT			=  7;
	const STATE_TAG_END				=  8;
	const STATE_XML					=  1;
	const STATE_PREPROCESSOR 		=  2;
	const STATE_COMMENT 			=  3;
	const STATE_CDATA				=  4;
	const STATE_DOCTYPE				=  5;
	const STATE_ATTRIBUTE_NAME		=  9;
	const STATE_ATTRIBUTE_CONTENT	= 10;
	
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
					case self::STATE_ROOT : 
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
						elseif($char === self::CHAR_TAG_OPEN && $oReader->isFollowedBy(self::CHAR_WHITESPACE)) {
							$this->noteProblem("It's invalid to use a whitespace after '<'.", $nLine, $nColumn);
						}
						elseif($char === self::CHAR_TAG_OPEN && $oReader->isFollowedBy(self::CHAR_TAG_CLOSE)) {
							$nState = self::STATE_TAG_SHORT;
							$oReader->setMark();
						}
						else {
							$nState = self::STATE_TAG_NAME;
							$oReader->setMark();
						}
						break;

						
						
					case self::STATE_TAG_NAME : 
						if($char === self::CHAR_WHITESPACE) {
							$sName = $oReader->getMark();
							$oBuilder->addTag($sName);
							$nState = self::STATE_TAG_CONTENT;
							echo $sName;
						}
						else if($char === self::CHAR_TAG_CLOSE) {
							$sName = $oReader->getMark();
							$oBuilder->addTag($sName, true);
							$nState = self::STATE_TAG_CONTENT;
							echo $sName;
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