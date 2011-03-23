<?php

class IPCO_ParserSettings extends IPCO_Base {
	
	const FILTER_IF 			= "if(%s) {\n";
	const FILTER_ELSEIF 		= "} else if(%s) {\n";
	const FILTER_ELSE			= "} else {\n";
	const FILTER_FOREACH		= "foreach(%s as %s) {parent::componentPush(%s);\n";
	const FILTER_WHILE			= "while(%s) {\n";
	
	const FILTER_END_IF			= "}\n";
	const FILTER_END_FOREACH	= "parent::componentPop();}\n";
	const FILTER_END_WHILE		= "}\n";
	
	const CALL_METHOD			= "parent::processMethod('%s', %s, %s)";
	const CALL_MEMBER			= "parent::processMember('%s', %s)";
	const CALL_SLICE			= "parent::processMember(%s, %s)";
	
	public static function getFilterIf($sCondition) {
		return sprintf(self::FILTER_IF, $sCondition);
	}
	
	public static function getFilterElseIf($sCondition) {
		return sprintf(self::FILTER_ELSEIF, $sCondition);
	}
	
	public static function getFilterElse() {
		return sprintf(self::FILTER_ELSE);
	}
	
	public static function getFilterForeach($sCondition) {
		return sprintf(self::FILTER_FOREACH, $sCondition, '$_comp', '$_comp');
	}
	
	public static function getFilterEndIf() {
		return sprintf(self::FILTER_END_IF);
	}
	
	public static function getFilterEndForeach() {
		return sprintf(self::FILTER_END_FOREACH);
	}
	
	public static function getCallMethod($sName, $sParams, $sBase) {
		return sprintf(self::CALL_METHOD, $sName, $sParams, $sBase);
	}
	
	public static function getCallMember($sName, $sBase) {
		return sprintf(self::CALL_MEMBER, $sName, $sBase);
	}
	
	public static function getCallSlice($sSlice, $sBase) {
		return sprintf(self::CALL_SLICE, $sSlice, $sBase);
	}
}

?>