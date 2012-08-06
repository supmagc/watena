<?php

abstract class IPCO_Processor extends IPCO_Base {
	
	private $m_aIndices = array();
	private $m_aComponents = array();
	private $m_oContentParser;
	private $m_sContent = null;
	
	public function __construct(IPCO $oIpco, IPCO_IContentParser $oContentParser = null) {
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
		if(!empty($mComponent)) {
			array_push($this->m_aComponents, IPCO_ComponentWrapper::createComponentWrapper($mComponent, parent::getIpco()));
			return true;
		}
		return false;
	}
	
	public function componentPop() {
		array_pop($this->m_aComponents);
	}
	
	public function indexPush($mIndex) {
		if(!empty($mIndex)) {
			array_push($this->m_aIndices, $mIndex);
			return true;
		}
		return false;
	}
	
	public function indexPop() {
		array_pop($this->m_aIndices);
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
		$oTemplate = $this->getIpco()->getCallbacks()->getTemplateForFilePath($sFilePath);
		$oTemplate->m_aComponents = $this->m_aComponents;
		return $oTemplate->getContent(true);
	}

	protected final function getIndex($mBase = null) {
		if(!empty($mBase))
			throw new IPCO_Exception(IPCO_Exception::INVALID_KEYWORDUSAGE);
		return array_last($this->m_aIndices);
	}
	
	protected final function getCurrent($mBase = null) {
		if(!empty($mBase))
			throw new IPCO_Exception(IPCO_Exception::INVALID_KEYWORDUSAGE);
		return array_last($this->m_aComponents);
	}
	
	protected final function processFirst($mBase = null) {
		$mReturn = null;
		NYI();
		$this->tryProcessFirst($mReturn, $mBase);
		return $mReturn;
	}
	
	protected final function processLast($mBase = null) {
		$mReturn = null;
		NYI();
		$this->tryProcessLast($mReturn, $mBase);
		return $mReturn;
	}
	
	protected final function processMethod($sName, array $aParams, $mBase = null) {
		$mReturn = null;
		$this->tryProcessMethod($mReturn, $sName, $aParams, $mBase);
		return $mReturn;
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
			
			if(!is_subclass_of($mBase, 'IPCO_ComponentWrapper')) 
				$mBase = IPCO_ComponentWrapper::createComponentWrapper($mBase, parent::getIpco());
				
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
			
			if(!is_subclass_of($mBase, 'IPCO_ComponentWrapper')) 
				$mBase = IPCO_ComponentWrapper::createComponentWrapper($mBase, parent::getIpco());
			
			return $mBase->tryGetProperty($mReturn, $sName);
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
			
			if(!is_subclass_of($mBase, 'IPCO_ComponentWrapper')) 
				$mBase = IPCO_ComponentWrapper::createComponentWrapper($mBase, parent::getIpco());
			
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

?>