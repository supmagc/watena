<?php

class IPCO_Exception extends Exception {

	const UNKNOWN = 0;
	
	const INVALID_COMPONENTTYPE = 1;
	const INVALID_EXPRESSION = 2;
	const INVALID_NESTING = 3;
	const INVALID_FILE = 4;
	const INVALID_KEYWORDUSAGE = 5;
	
	const TEMPLATETOFILE_UNCALLABLE = 10;
	const TEMPLATETOFILE_INVALID_FILE = 11;
	
	const FILTER_REGION_NO_TAG = 20;
	const FILTER_REGION_NO_NAME = 21;
	const FILTER_REGION_END_CURRENT_MISMATCH = 22;
	const FILTER_REGION_END_NONE_CURRENT = 23;
	const FILTER_REGION_BEGIN_HAS_CURRENT = 24;
	const FILTER_EXTENDS_INVALID_FILE = 25;

	public function __construct($nCode) {
		parent::__construct(self::getCodedMessage($nCode), $nCode);
	}
	
	public static function getCodedMessage($nCode) {
		switch($nCode) {
			case self::UNKNOWN :
				return 'Unknown IPCO-Exception.';
			case self::FILTER_REGION_NO_TAG :
				return 'The provided region-filter could not be processed since there is no operation specified.';
			case self::FILTER_REGION_NO_NAME :
				return 'The provided region-filter could not be processed since there is no name provided.';
			case self::FILTER_REGION_END_CURRENT_MISMATCH :
				return 'The closing region name does not match the current region name.';
			case self::FILTER_REGION_END_NONE_CURRENT :
				return 'Unable to close a region when no region is open.';
			case self::FILTER_REGION_BEGIN_HAS_CURRENT :
				return 'Unable to open a region when another region is still open.';
			default :
				return 'An IPCO-Excpeption was encountered with an unknwon code: ' . $nCode;
		}
	}
}

?>