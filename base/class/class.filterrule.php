<?php namespace Watena\Core;
/**
 * Holds a filter-rule for a filter.
 * This is used to calculate the validity of a filter for a specific mapping.
 * 
 * A rule consists of:
 * - A variable name
 * - A condition
 * - Condition value
 * 
 * When matching, a valid variables based on variablename will be searched.
 * If the variablename contains '.' (dot) it will be split and the splitted 
 * values will be used as array indices for the variable.
 * The final variable will be checked against the condition (and condition value is applicable).
 * The following conditions are supported:
 * - regex, pattern: Check if the variable matches the condition.
 * - null, notnull: Check if the variable is null or not null.
 * - false, true: Check if the variable is true or false.
 * - set, notset: Check if the variable is set or not.
 * - begin, begins, beginwith, begintswith: Check if the variable begins with the given value.
 * - end, ends, endwith, endswith: Check if the variable ends with the given value.
 * - gt, greater, greaterthan: Check if the variable is greater than the given value.
 * - ls, less, lesser, lessthan, lesserthan: Check if the variable is lesser than the given value.
 * - is, eq, equal, equals: Check if the variavle if equal to the given value.
 * 
 * @see Filter
 * @see Mapping
 * @see FilterData
 * @see FilterGroup
 * @author Jelle
 * @version 0.2.0
 */
class FilterRule extends Object {
	
	private $m_sVariableName;
	private $m_aVariableKeys;
	private $m_sCondition;
	private $m_sContent;
	
	/**
	 * Create a new rule.
	 * 
	 * @param string $sVariableName
	 * @param string $sCondition
	 * @param string $sContent
	 */
	public function __construct($sVariableName, $sCondition, $sContent) {
		$this->m_aVariableKeys = explode_trim('.', $sVariableName);
		$this->m_sVariableName = $sVariableName;
		$this->m_sCondition = Encoding::toLower($sCondition);
		$this->m_sContent = $sContent;
	}
	
	/**
	 * Get the full variable name.
	 * 
	 * @return string
	 */
	public final function getVariableName() {
		return $this->m_sVariableName;
	}

	/**
	 * Get the splitted variable name.
	 * 
	 * @return array<string>
	 */
	public final function getVariableKeys() {
		return $this->m_aVariableKeys;
	}

	/**
	 * Get the condition string.
	 * 
	 * @return string
	 */
	public final function getCondition() {
		return $this->m_sCondition;
	}
	
	/**
	 * Get the optional variable rule value.
	 * 
	 * @return string
	 */
	public final function getContent() {
		return $this->m_sContent;
	}
	
	/**
	 * Check if the given mapping is match against this filterrule.
	 * 
	 * @param Mapping $oMapping
	 * @return boolean
	 */
	public final function matches(Mapping $oMapping) {
		$mVariable = $oMapping->getVariable($this->m_aVariableKeys);
		switch($this->m_sCondition) {
			case 'regex': 
			case 'pattern': if(Encoding::regMatch($this->m_sContent, $mVariable)) return true; break;
			case 'null': if(null === $mVariable) return true; break;
			case 'notnull': if(null !== $mVariable) return true; break;
			case 'false': if(!$mVariable) return true; break;
			case 'true': if($mVariable) return true; break;
			case 'set': if(!empty($mVariable)) return true; break;
			case 'notset': if(empty($mVariable)) return true; break;
			case 'begin':
			case 'begins': 
			case 'beginwith':
			case 'beginswith': if(Encoding::beginsWith($mVariable, $this->m_sContent, false)) return true; break;
			case 'end':
			case 'ends': 
			case 'endwith':
			case 'endswith': if(Encoding::endsWith($mVariable, $this->m_sContent, false)) return true; break;
			case 'gt':
			case 'greater':
			case 'greaterthan': if($mVariable > $this->m_sContent) return true; break;
			case 'lt':
			case 'less':
			case 'lesser':
			case 'lessthan':
			case 'lesserthan': if($mVariable < $this->m_sContent) return true; break;
			case 'is':
			case 'eq':
			case 'equal':
			case 'equals': if($this->m_sContent == $mVariable) return true; break;
			default: return false;
		}
		return false;
	}
}
