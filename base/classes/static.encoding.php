<?php

class Encoding {
	
	private static $s_sEncoding = null;
	
	public static function init($sCharset) {
		self::$s_sEncoding = $sCharset;
		mb_detect_order(array('UTF-8', 'UTF-7', 'ISO-8859-1', 'ASCII', 'EUC-JP', 'SJIS', 'eucJP-win', 'SJIS-win', 'JIS', 'ISO-2022-JP'));
		mb_internal_encoding(self::$s_sEncoding);
		mb_regex_encoding(self::$s_sEncoding);
	}
	
	public static function charset() {
		return self::$s_sEncoding;
	}
	
	public static function convert($sData, $sEncoding = null) {
		return mb_convert_encoding($sData, $sEncoding === null ? self::$s_sEncoding : $sEncoding, mb_detect_encoding($sData));
	}
	
	public static function convertByRef(&$sData, $sEncoding = null) {
		$sData = mb_convert_encoding($sData, $sEncoding === null ? self::$s_sEncoding : $sEncoding, mb_detect_encoding($sData));
	}
	
	public static function substring($sData, $nStart, $nLength = null, $sEncoding = null) {
		return mb_substr($sData, $nStart, $nLength === null ? (self::length($sData, $sEncoding) - $nStart) : $nLength, $sEncoding === null ? self::$s_sEncoding : $sEncoding);
	}
	
	public static function trim($sData, $sEncoding = null) {
		return trim($sData);
	}
	
	public static function contains($sData, $sSearch, $bCaseInsensitive = true, $sEncoding = null) {
		if($bCaseInsensitive) return mb_stristr($sData, $sSearch, true, $sEncoding === null ? self::$s_sEncoding : $sEncoding) !== false;
		else return mb_strstr($sData, $sSearch, true, $sEncoding === null ? self::$s_sEncoding : $sEncoding) !== false;
	}
	
	public static function stringToUpper($sData, $sEncoding = null) {
		return mb_strtoupper($sData, $sEncoding === null ? self::$s_sEncoding : $sEncoding);
	}
	
	public static function stringToLower($sData, $sEncoding = null) {
		return mb_strtolower($sData, $sEncoding === null ? self::$s_sEncoding : $sEncoding);
	}
	
	public static function stringReplace($mSearch, $mReplace, $sData, $bCaseInsensitive = true, $sEncoding = null) {
		if(!is_array($mSearch)) $mSearch = array($mSearch);
    	foreach($mSearch as $nSearch => $sSearch) {
    		$nOffset = 0;
    		$sReplace = is_array($mReplace) ? $mReplace[$nSearch] : $mReplace;
	        while(($nIndex = self::IndexOf($sData, $sSearch, $nOffset, $bCaseInsensitive, $sEncoding)) !== false) { 
	    		$nOffset = $nIndex + self::length($sReplace, $sEncoding); 
	    		$sData = self::substring($sData, 0, $nIndex). $sReplace . self::substring($sData, $nIndex + self::length($sSearch, $sEncoding));
	        }
    	}
        return $sData; 
	}
	
	public static function beginsWith($sData, $sSearch, $bCaseInsensitive = true, $sEncoding = null) {
		if($bCaseInsensitive) {
			$sData = self::stringToLower($sData, $sEncoding);
			$sSearch = self::stringToLower($sSearch, $sEncoding);
		}
		return self::substring($sData, 0, self::Length($sSearch, $sEncoding), $sEncoding) == $sSearch;
	}
	
	public static function endsWith($sData, $sSearch, $bCaseInsensitive = true, $sEncoding = null) {
		if($bCaseInsensitive) {
			$sData = self::stringToLower($sData, $sEncoding);
			$sSearch = self::stringToLower($sSearch, $sEncoding);
		}
		$nLength = self::Length($sSearch, $sEncoding);
		return self::substring($sData, self::length($sData) - $nLength, $nLength, $sEncoding) == $sSearch;
	}
	
	public static function indexOf($sData, $sSearch, $nOffset = 0, $bCaseInsensitive = false, $sEncoding = null) {
		if($bCaseInsensitive) {
			return mb_stripos($sData, $sSearch, $nOffset, $sEncoding === null ? self::$s_sEncoding : $sEncoding);
		}
		else {
			return mb_strpos($sData, $sSearch, $nOffset, $sEncoding === null ? self::$s_sEncoding : $sEncoding);
		}
	}
	
	public static function length($sData, $sEncoding = null) {
		return mb_strlen($sData, $sEncoding === null ? self::$s_sEncoding : $sEncoding);
	}
	
	public static function regMatch($sPatern, $sData, $sOptions = 'msr', $sEncoding = null) {
		if($sEncoding !== null) mb_regex_encoding($sEncoding);
		mb_ereg_search_init($sData, $sPatern, $sOptions);
		$bReturn = mb_ereg_search();
		if($sEncoding !== null) mb_regex_encoding(self::$s_sEncoding);
		return $bReturn;
	}
	
	public static function regFind($sPatern, $sData, array &$aMatches = array(), array &$aPositions = array(), $sOptions = 'msr', $sEncoding = null) {
		if($sEncoding !== null) mb_regex_encoding($sEncoding);
		mb_ereg_search_init($sData, $sPatern, $sOptions);
		$bReturn = mb_ereg_search();
		if($bReturn) {
			if($aMatches !== null) $aMatches = mb_ereg_search_getregs();
			if($aPositions !== null) {
				$aTmp = $aMatches === null ? mb_ereg_search_getregs() : $aMatches;
				$aPositions = array(mb_ereg_search_getpos() - self::length($aTmp[0]), mb_ereg_search_getpos());
			}
		}
		if($sEncoding !== null) mb_regex_encoding(self::$s_sEncoding);
		return $bReturn;
	}

	public static function regFindAll($sPatern, $sData, array &$aMatches = array(), array &$aPositions = array(), $sOptions = 'msr', $sEncoding = null) {
		if($sEncoding !== null) mb_regex_encoding($sEncoding);
		$nCount = 0;
		$aMatches = array();
		$aPositions = array();
		mb_ereg_search_init($sData, $sPatern, $sOptions);
		while(mb_ereg_search()) {
			$aMatches []= mb_ereg_search_getregs();
			$aPositions []= array(mb_ereg_search_getpos() - self::length($aMatches[$nCount][0]), mb_ereg_search_getpos());
			mb_ereg_search_setpos(mb_ereg_search_getpos());
			++$nCount;
		}
		if($sEncoding !== null) mb_regex_encoding(self::$s_sEncoding);
		return $nCount;
	}
	
	public static function regReplace($sPatern, $sReplace, $sData, $sOptions = 'msr', $sEncoding = null) {
		if($sEncoding !== null) mb_regex_encoding($sEncoding);
		$sData = mb_ereg_replace($sPatern, $sReplace, $sData, $sOptions);
		if($sEncoding !== null) mb_regex_encoding(self::$s_sEncoding);
		return $sData;
	}
	
	public static function regEncode($sData, $sEncoding = null) {
		$sList = '.\\+*?\[\^\]$(){}=!<>|:\-';
		return self::regReplace("([$sList])", "\\\\1", $sData, 'msr', $sEncoding);
	}
}

?>