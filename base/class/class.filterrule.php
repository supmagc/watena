<?php

class FilterRule extends Object {
	
	private $m_sVariableName;
	private $m_aVariableKeys;
	private $m_sCondition;
	private $m_sContent;
	
	public function __construct($sVariableName, $sCondition, $sContent) {
		$this->m_aVariableKeys = explode_trim('.', $sVariableName);
		$this->m_sVariableName = $sVariableName;
		$this->m_sCondition = Encoding::toLower($sCondition);
		$this->m_sContent = $sContent;
	}
	
	public final function getVariableName() {
		return $this->m_sVariableName;
	}

	public final function getVariableKeys() {
		return $this->m_aVariableKeys;
	}
	
	public final function getCondition() {
		return $this->m_sCondition;
	}
	
	public final function getContent() {
		return $this->m_sContent;
	}
	
	public final function matches(Mapping $oMapping) {
		$mVariable = $oMapping->getVariable($this->m_aVariableKeys);
		switch($this->m_sCondition) {
			case 'regex': 
			case 'pattern': if(Encoding::regMatch($this->m_sContent, $mVariable)) return true;
			case 'null': if(null === $mVariable) return true;
			case 'notnull': if(null !== $mVariable) return true;
			case 'false': if(!$mVariable) return true;
			case 'true': if($mVariable) return true;
			case 'set': if(!empty($mVariable)) return true;
			case 'notset': if(empty($mVariable)) return true;
			case 'begin':
			case 'begins': 
			case 'beginwith':
			case 'beginswith': if(Encoding::beginsWith($mVariable, $this->m_sContent, false)) return true;
			case 'end':
			case 'ends': 
			case 'endwith':
			case 'endswith': if(Encoding::endsWith($mVariable, $this->m_sContent, false)) return true;
			case 'gt':
			case 'greater':
			case 'greaterthan': if($mVariable > $this->m_sContent) return true;
			case 'lt':
			case 'less':
			case 'lesser':
			case 'lessthan':
			case 'lesserthan': if($mVariable < $this->m_sContent) return true;
			case 'is':
			case 'eq':
			case 'equal':
			case 'equals': if($this->m_sContent == $mVariable) return true;
			default: return false;
		}
	}
}

?>