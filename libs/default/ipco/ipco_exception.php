<?php

class IPCO_Exception extends Exception {

	const UNKNOWN = 0;
	
	const INVALIDCOMPONENTTYPE = 1;
	const INVALIDEXPRESSION = 2;
	const INVALIDNESTING = 3;
	const FILTER_INCLUDE_NO_NAME = 8;
	
	const TEMPLATETOFILE_UNCALLABLE = 4;
	const TEMPLATETOFILE_INVALID_FILE = 5;
	const INVALID_FILE = 13;
	
	const FILTER_REGION_NO_TAG = 6;
	const FILTER_REGION_NO_NAME = 7;
	const FILTER_REGION_END_CURRENT_MISMATCH = 9;
	const FILTER_REGION_END_NONE_CURRENT = 10;
	const FILTER_REGION_BEGIN_HAS_CURRENT = 11;
	const FILTER_EXTENDS_INVALID_FILE = 12;

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