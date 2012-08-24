<?php

class Encoding {
	
	private static $s_sEncoding = 'UTF-8';
	
	public static function init($sCharset) {
		self::$s_sEncoding = $sCharset;
		mb_detect_order(array('UTF-8', 'UTF-7', 'ISO-8859-1', 'ASCII', 'EUC-JP', 'SJIS', 'eucJP-win', 'SJIS-win', 'JIS', 'ISO-2022-JP'));
		mb_internal_encoding(self::$s_sEncoding);
		mb_regex_encoding(self::$s_sEncoding);
		ini_set('default_charset', $sCharset);
		$_GET = array_map(array('Encoding', 'convert'), $_GET);
		$_POST = array_map(array('Encoding', 'convert'), $_POST);
		$_COOKIE = array_map(array('Encoding', 'convert'), $_COOKIE);
	}
	
	public static function charset() {
		return self::$s_sEncoding;
	}
	
	public static function convert($sData, $sEncoding = null) {
		self::convertByRef($sData, $sEncoding);
		return $sData;
	}
	
	public static function convertByRef(&$sData, $sEncoding = null) {
		$sData = (($sTemp = mb_detect_encoding($sData)) !== false && $sTemp != ($sEncoding === null ? self::$s_sEncoding : $sEncoding)) ? mb_convert_encoding($sData, $sEncoding === null ? self::$s_sEncoding : $sEncoding, $sTemp) : $sData;
	}
	
	public static function substring($sData, $nStart, $nLength = null, $sEncoding = null) {
		return mb_substr($sData, $nStart, $nLength === null ? (self::length($sData, $sEncoding) - $nStart) : $nLength, $sEncoding === null ? self::$s_sEncoding : $sEncoding);
	}
	
	public static function trim($sData, $sChars = null, $sEncoding = null) {
		return $sChars ? trim($sData, $sChars) : trim($sData);
	}
	
	public static function trimEnd($sData, $sChars = null, $sEncoding = null) {
		return $sChars ? rtrim($sData, $sChars) : rtrim($sData);
	}
	
	public static function trimBegin($sData, $sChars = null, $sEncoding = null) {
		return $sChars ? ltrim($sData, $sChars) : ltrim($sData);
	}
	
	public static function contains($sData, $sSearch, $bCaseInsensitive = true, $sEncoding = null) {
		if($bCaseInsensitive) return mb_stristr($sData, $sSearch, true, $sEncoding === null ? self::$s_sEncoding : $sEncoding) !== false;
		else return mb_strstr($sData, $sSearch, true, $sEncoding === null ? self::$s_sEncoding : $sEncoding) !== false;
	}
	
	public static function toUpper($sData, $sEncoding = null) {
		return mb_strtoupper($sData, $sEncoding === null ? self::$s_sEncoding : $sEncoding);
	}
	
	public static function toLower($sData, $sEncoding = null) {
		return mb_strtolower($sData, $sEncoding === null ? self::$s_sEncoding : $sEncoding);
	}
	
	public static function replace($mSearch, $mReplace, $sData, $bCaseInsensitive = true, $sEncoding = null) {
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
	
	public static function translate($sSearch, $sReplace, $sData, $bCaseInsensitive = true, $sEncoding = null) {
		return self::replace(str_split($sSearch), str_split($sReplace), $sData, $bCaseInsensitive, $sEncoding);
	}
	
	public static function beginsWith($sData, $sSearch, $bCaseInsensitive = true, $sEncoding = null) {
		if($bCaseInsensitive) {
			$sData = self::toLower($sData, $sEncoding);
			$sSearch = self::toLower($sSearch, $sEncoding);
		}
		return self::substring($sData, 0, self::Length($sSearch, $sEncoding), $sEncoding) == $sSearch;
	}
	
	public static function endsWith($sData, $sSearch, $bCaseInsensitive = true, $sEncoding = null) {
		if($bCaseInsensitive) {
			$sData = self::toLower($sData, $sEncoding);
			$sSearch = self::toLower($sSearch, $sEncoding);
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
	
	public static function lastIndexOf($sData, $sSearch, $nOffset = 0, $bCaseInsensitive = false, $sEncoding = null) {
		if($bCaseInsensitive) {
			return mb_strripos($sData, $sSearch, $nOffset, $sEncoding === null ? self::$s_sEncoding : $sEncoding);
		}
		else {
			return mb_strrpos($sData, $sSearch, $nOffset, $sEncoding === null ? self::$s_sEncoding : $sEncoding);
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
	
	public static function regReplaceAll($sPatern, $sReplace, $sData, $sOptions = 'msr', $sEncoding = null) {
		if($sEncoding !== null) mb_regex_encoding($sEncoding);
		while(($sTemp = mb_ereg_replace($sPatern, $sReplace, $sData, $sOptions)) !== $sData) {
			$sData = $sTemp;
		}
		if($sEncoding !== null) mb_regex_encoding(self::$s_sEncoding);
		return $sData;
	}
	
	public static function regSplit($sPatern, $sData, $sEncoding = null) {
		if($sEncoding !== null) mb_regex_encoding($sEncoding);
		$aData = mb_split($sPatern, $sData);
		if($sEncoding !== null) mb_regex_encoding(self::$s_sEncoding);
		return $aData;
		
	}
	
	public static function regEncode($sData, $sEncoding = null) {
		$sList = '.\\+*?\[\^\]$(){}=!<>|:\-';
		return self::regReplace("([$sList])", "\\\\1", $sData, 'msr', $sEncoding);
	}
}

?>