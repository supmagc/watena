<?php

class ToString {

	const NONE = 0;
	const MULTILINE = 1;
	const INDENTED = 2;
	const QUOTED = 4;
	const CHOP_STRING = 8;
	const CHOP_ARRAY = 16;
	
	private static $s_nFlags;
	private static $s_nRecursions;
	
	private $m_sKey = null;
	private $m_sName = 'Unknown type';
	private $m_aSubData = null;
	
	private function __construct($mData, $sKey = null) {
		$this->m_sKey = $sKey;
		
		if(self::$s_nRecursions++ < 10) {
			if(!$this->parseCallback($mData) &&
			   !$this->parseString($mData) &&
			   !$this->parseNumeric($mData) &&
			   !$this->parseConst($mData) &&
			   !$this->parseResource($mData) &&
			   !$this->parseArray($mData) &&
			   !$this->parseObject($mData)
			) {
				$this->m_sKey = null;
				$this->m_sName = 'Unknown type';
				$this->m_aSubData = null;
			}
		}
		else {
			$this->m_sKey = null;
			$this->m_sName = 'Recursion limit';
			$this->m_aSubData = null;
		}
		--self::$s_nRecursions;
	}
	
	private function parseCallback($cbData) {
		if(is_callable($cbData, false, $this->m_sName)) {
			return true;
		}
		
		if(is_string($cbData) && function_exists($cbData)) {
			$this->m_sName = $cbData;
			return true;
		}
			
		if(is_array($cbData) && count($cbData) == 2 && isset($cbData[0]) && isset($cbData[1]) && is_string($cbData[1])) {
			if(is_object($cbData[0])) {
				$this->m_sName = get_class($cbData[0]) . '::' . $cbData[1];
				return true;
			}
			
			if(is_string($cbData[0]) && class_exists($cbData[0])) {
				$this->m_sName = $cbData[0] . '::' . $cbData[1];
				return true;
			}
		}
	}
	
	private function parseString($sData) {
		if(is_string($sData)) {
			if(self::$s_nFlags & self::CHOP_STRING && Encoding::length($sData) > 50) {
				$sData = Encoding::substring($sData, 0, 50) . '...';
			}
			
			if(self::$s_nFlags & self::QUOTED) {
				$sData = var_export($sData, true);
			}
			
			$this->m_sName = $sData;
			return true;
		}
	}
	
	private function parseNumeric($nData) {
		if(is_numeric($nData)) {
			$this->m_sName = strval($nData);
			return true;
		}
	}
	
	private function parseConst($mData) {
		if($mData === true) {
			$this->m_sName = 'True';
			return true;
		}
		if($mData === false) {
			$this->m_sName = 'False';
			return true;
		}
		if($mData === null) {
			$this->m_sName = 'Null';
			return true;
		}
	}
	
	private function parseResource($hData) {
		if(is_resource($hData)) {
			$this->m_sName = 'Resource';
			return true;
		}
	}
	
	private function parseArray($aData) {
		if(is_array($aData)) {
			if(self::$s_nFlags & self::CHOP_ARRAY && count($aData) > 25) {
				$aData = array_slice($aData, 0, 25);
				array_push($aData, '...');
			}
			
			$this->m_sName = 'Array';
			$this->m_aSubData = array();
			foreach($aData as $mKey => $mValue) {
				$oSubData = new ToString($mValue, strval($mKey));
				$this->m_aSubData []= $oSubData;
			}
			return true;
		}
	}
	
	private function parseObject($oData) {
		if(is_object($oData)) {
			$this->m_sName = 'Object['.get_class($o_sData).']';
			$this->m_aSubData = array();
			$aProperties = new ReflectionClass($oData);
			foreach($aProperties as $oProperty) {
				$oProperty->setAccesible(true);
				$oSubData = new ToString($oProperty->getValue($oData), $oProperty->getName());
				$this->m_aSubData []= $oSubData;
			}
			return true;
		}
	}
	
	public static function parse($mData, $nFlags = self::NONE) {
		self::$s_nRecursions = 0;
		self::$s_nFlags = $nFlags;
		
		$oData = new ToString($mData);
		return self::parseInstance($oData);
	}
	
	private static function parseInstance(ToString $oToString, $sIndentation = '') {
		$sReturn = '';
		if(self::$s_nFlags & self::INDENTED) $sReturn .= $sIndentation;
		if($oToString->m_sKey)  $sReturn .= $oToString->m_sKey . ': ';
		$sReturn .= $oToString->m_sName;
		if(is_array($oToString->m_aSubData)) {
			$sReturn .= '(';
			if(count($oToString->m_aSubData) > 0 && self::$s_nFlags & self::MULTILINE) $sReturn .= "\n";
			foreach($oToString->m_aSubData as $oSubData) {
				$sReturn .= self::parseInstance($oSubData, $sIndentation . "\t");
			}
			if(count($oToString->m_aSubData) > 0 && self::$s_nFlags & self::INDENTED) $sReturn .= $sIndentation;
			$sReturn .= ')';
		}
		if(self::$s_nFlags & self::MULTILINE) $sReturn .= "\n";
		return $sReturn;
	}
}