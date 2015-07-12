<?php namespace Watena\Libs\Base\Ipco;

use function Watena\Core\array_last;

abstract class Processor extends Base {
	
	private $m_aVars = array();
	private $m_aIndices = array();
	private $m_aComponents = array();
	private $m_oContentParser;
	private $m_sContent = null;
	
	public function __construct(IPCO $oIpco, IContentParser $oContentParser = null) {
		parent::__construct($oIpco);
		$this->m_oContentParser = $oContentParser;
	}
	
	public function generate() {
		return 'Empty template';
	}
	
	public function getContent($bForceRenewal = false) {
		if($this->m_sContent === null || $bForceRenewal)
			$this->m_sContent = $this->generate();
		return $this->m_sContent;
	}
	
	public function componentPush($mComponent) {
		array_push($this->m_aComponents, ComponentWrapper::createComponentWrapper($mComponent, parent::getIpco()));
	}
	
	public function componentPop() {
		array_pop($this->m_aComponents);
	}
	
	public function indexPush($mIndex) {
		array_push($this->m_aIndices, $mIndex);
	}
	
	public function indexPop() {
		array_pop($this->m_aIndices);
	}
	
	protected final function getIndex() {
		return array_last($this->m_aIndices);
	}
	
	protected final function getCurrent() {
		return array_last($this->m_aComponents);
	}
	
	protected final function callContentParser($sMethod, array $aParams) {
		if($this->m_oContentParser !== null) {
			if(!method_exists($this->m_oContentParser, $sMethod))
				return null;
			else
				return call_user_func_array(array($this->m_oContentParser, $sMethod), $aParams);
		}
		return '';
	}
	
	protected final function callInclude($sFilePath) {
		$oTemplate = $this->getIpco()->getCallbacks()->getTemplateClassForFilePath($sFilePath);
		$oTemplate->m_aComponents = $this->m_aComponents;
		$oTemplate->m_aIndices = $this->m_aIndices;
		$oTemplate->m_aVars = $this->m_aVars;
		return $oTemplate->getContent(true);
		// TODO: should this be reverted
	}
	
	protected final function processFirst($mBase = null) {
		if(!$mBase) 
			$mBase = $this->getCurrent();
		
		if(!$mBase)
			return null;
		
		if(!is_subclass_of($mBase, 'ComponentWrapper'))
			$mBase = ComponentWrapper::createComponentWrapper($mBase, parent::getIpco());
		
		return $mBase->getFirst();
	}
	
	protected final function processLast($mBase = null) {
		if(!$mBase) 
			$mBase = $this->getCurrent();
		
		if(!$mBase)
			return null;
		
		if(!is_subclass_of($mBase, 'ComponentWrapper'))
			$mBase = ComponentWrapper::createComponentWrapper($mBase, parent::getIpco());
		
		return $mBase->getLast();
	}
	
	protected final function processMethod($sName, array $aParams, $mBase = null) {
		$mReturn = null;
		$this->tryProcessMethod($mReturn, $sName, $aParams, $mBase);
		return $mReturn;
	}
	
	protected final function processVarSet($sName, $mValue = null) {
		$this->m_aVars[$sName] = $mValue ?: 0;
	}
	
	protected final function processVarIncrease($sName, $mValue = null) {
		if(!isset($this->m_aVars[$sName]))
			$this->m_aVars[$sName] = 0;
		$this->m_aVars[$sName] += $mValue ?: 1;
	}
	
	protected final function processVarDecrease($sName, $mValue = null) {
		if(!isset($this->m_aVars[$sName]))
			$this->m_aVars[$sName] = 0;
		$this->m_aVars[$sName] -= $mValue ?: 1;
	}
	
	protected final function processMember($sName, $mBase = null) {
		$mReturn = null;
		$this->tryProcessMember($mReturn, $sName, $mBase);
		return $mReturn;
	}
	
	protected final function processSlices(array $aSliced, $mBase = null) {
		$mReturn = null;
		$this->tryProcessSlices($mReturn, $aSliced, $mBase);
		return $mReturn;
	}
	
	protected final function tryProcessMethod(&$mReturn, $sName, array $aParams, $mBase = null) {
		if(!empty($mBase)) {
			
			if(!is_subclass_of($mBase, 'ComponentWrapper'))
				$mBase = ComponentWrapper::createComponentWrapper($mBase, parent::getIpco());
				
			return $mBase->tryGetMethod($mReturn, $sName, $aParams);
		}
		else {
			for($i=count($this->m_aComponents) - 1 ; $i>=0 ; --$i) {
				$bReturn = self::tryProcessMethod($mReturn, $sName, $aParams, $this->m_aComponents[$i]);
				if($bReturn) return true;
			}			
		}
		return false;
	}
	
	protected final function tryProcessMember(&$mReturn, $sName, $mBase = null) {
		if(!empty($mBase)) {
			
			if(!is_subclass_of($mBase, 'ComponentWrapper'))
				$mBase = ComponentWrapper::createComponentWrapper($mBase, parent::getIpco());
			
			return $mBase->tryGetProperty($mReturn, $sName);
		}
		else if(isset($this->m_aVars[$sName])) {
			$mReturn = $this->m_aVars[$sName];
			return true;
		}
		else {
			for($i=count($this->m_aComponents) - 1 ; $i>=0 ; --$i) {
				$bReturn = self::tryProcessMember($mReturn, $sName, $this->m_aComponents[$i]);
				if($bReturn) return true;
			}
			if(isset($_POST[$sName])) {
				$mReturn = $_POST[$sName];
				return true;
			}
			if(isset($_GET[$sName])) {
				$mReturn = $_GET[$sName];
				return true;
			}
		}
		return false;
	}
	
	protected final function tryProcessSlices(&$mReturn, array $aSliced, $mBase = null) {
		if(!empty($mBase)) {
			
			if(!is_subclass_of($mBase, 'ComponentWrapper'))
				$mBase = ComponentWrapper::createComponentWrapper($mBase, parent::getIpco());
			
			$mRoot = $this->m_aComponents[$i];
			$bReturn = true;
			foreach($aSliced as $mSlice) {
				
				
				if(is_array($mRoot) && isset($mRoot[$mSlice])) {
					$mReturn = &$mRoot[$mSlice];
					$bReturn = true;
				}
				else {
					$bReturn = false;
					break;
				}
			}
			return $bReturn;
		}
		else {
			for($i=count($this->m_aComponents) - 1 ; $i>=0 ; --$i) {
				$bReturn = self::tryProcessSlices($aSliced, $this->m_aComponents[$i]);
				if($bReturn) return true;
			}
		}
		return null;
	}
}
