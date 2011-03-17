<?php

class IPCO_Processor {
	
	private $m_aComponents = array();
	
	public function componentPush($mComponent) {
		if(!empty($mComponent)) {
			array_push($this->m_aComponents, $mComponent);
			return true;
		}
		return false;
	}
	
	public function componentPop() {
		array_pop($this->m_aComponents);
	}
	
	protected final function processMethod($sName, array $aParams, $mBase = null) {
		static $bReturn = false;
		if(!empty($mBase)) {
			if(method_exists($mBase, $sName)) {
				$bReturn = true;
				return call_user_func_array(array($mBase, $sName), $aParams);
			}
		}
		else {
			$bReturn = false;
			for($i=count($this->m_aComponents) - 1 ; $i>=0 ; --$i) {
				$mBase = $this->processMethod($sName, $aParams, $this->m_aComponents[$i]);
				if($bReturn) return $mBase;
			}			
		}
		return null;
	}
	
	protected final function processMember($sName, $mBase = null) {
		static $bReturn = false;
		if(!empty($mBase)) {
			if(property_exists($mBase, $sName)) {
				$bReturn = true;
				return $mBase->$sName;
			}
			else if(array_key_exists($sName, get_class_vars(get_class($mBase)))) {
				$bReturn = true;
				return $mBase->$sName;
			}
			else if(is_array($mBase) && isset($mBase[$sName])) {
				$bReturn = true;
				return $mBase[$sName];
			}
			else if(method_exists($mBase, $sName)) {
				$bReturn = true;
				return call_user_func(array($mBase, $sName));
			}
		}
		else {
			$bReturn = false;
			for($i=count($this->m_aComponents) - 1 ; $i>=0 ; --$i) {
				echo 'Found component';
				$mBase = $this->processMember($sName, $this->m_aComponents[$i]);
				if($bReturn) {
					echo 'return was true';
					return $mBase;
				}
			}
		}
		return null;
	}
	
	protected final function processSlices(array $aSliced, $mBase = null) {
		static $bReturn = false;
		if(!empty($mBase)) {
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
				$mBase = $this->processSlices($aSliced, $this->m_aComponents[$i]);
				if($bReturn) return $mBase;
			}
		}
		return null;
	}
}

?>