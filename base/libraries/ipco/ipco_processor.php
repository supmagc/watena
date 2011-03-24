<?php

class IPCO_Processor extends IPCO_Base {
	
	private $m_aComponents = array();
	
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
	
	protected final function processMethod($sName, array $aParams, $mBase = null) {
		static $bReturn = false;
		$mReturn = null;
		if(!empty($mBase)) {
			if(!is_subclass_of($mBase, 'IPCO_ComponentWrapper')) $mBase = IPCO_ComponentWrapper::createComponentWrapper($mBase, parent::getIpco());
			$bReturn = $mBase->tryGetMethod($mReturn, $sName, $aParams);
			return $mReturn;
		}
		else {
			$bReturn = false;
			for($i=count($this->m_aComponents) - 1 ; $i>=0 ; --$i) {
				$mReturn = self::processMethod($sName, $aParams, $this->m_aComponents[$i]);
				if($bReturn) return $mReturn;
			}			
		}
		return null;
	}
	
	protected final function processMember($sName, $mBase = null) {
		static $bReturn = false;
		$mReturn = null;
		if(!empty($mBase)) {
			if(!is_subclass_of($mBase, 'IPCO_ComponentWrapper')) $mBase = IPCO_ComponentWrapper::createComponentWrapper($mBase, parent::getIpco());
			$bReturn = $mBase->tryGetProperty($mReturn, $sName);
			return $mReturn;
		}
		else {
			$bReturn = false;
			for($i=count($this->m_aComponents) - 1 ; $i>=0 ; --$i) {
				$mReturn = self::processMember($sName, $this->m_aComponents[$i]);
				if($bReturn) return $mReturn;
			}
		}
		return null;
	}
	
	protected final function processSlices(array $aSliced, $mBase = null) {
		static $bReturn = false;
		$mReturn = null;
		if(!empty($mBase)) {
			if(!is_subclass_of($mBase, 'IPCO_ComponentWrapper')) $mBase = IPCO_ComponentWrapper::createComponentWrapper($mBase, parent::getIpco());
			
			$mRoot = $this->m_aComponents[$i];
			foreach($aSliced as $mSlice) {
				
				
				if(is_array($mRoot) && isset($mRoot[$mSlice])) {
					$bReturn = true;
					$mRoot = &$mRoot[$mSlice];
				}
				else {
					$bReturn = false;
					break;
				}
			}
			if($bReturn) return $mRoot;
		}
		else {
			$bReturn = false;
			for($i=count($this->m_aComponents) - 1 ; $i>=0 ; --$i) {
				$mReturn = self::processSlices($aSliced, $this->m_aComponents[$i]);
				if($bReturn) return $mReturn;
			}
		}
		return null;
	}
}

?>