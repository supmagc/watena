<?php namespace Watena\Core;

class ComponentFactory extends Object {
	
	private $m_aClasses = array();
	private $m_aLinkage = array();
	
	public final function registerComponent($sClass, $sPath, $sPreferredLibrary = null) {
		$oComponent = ComponentLoader::create($sClass, $sPath);
		if(!isset($this->m_aClasses)) {
			if(empty($sPath) || include_safe($sPath)) {
				if(class_exists($sClass)) {
					$aTypes = array_merge(array($sClass), class_parents($sClass), class_implements($sClass));
					foreach($aTypes as $sType) {
						if(!isset($this->m_aLinkage[$sType])) {
							$this->m_aLinkage[$sType] = array();
						}
						$this->m_aLinkage[$sType] []= $sClass;
					}
					$this->m_aClasses[$sClass] = $sPath;
					$this->getLogger()->info('The component {class} is registered.', array('class' => $sClass, 'path' => $sPath));
				}
				else {
					$this->getLogger()->error('The component {class} could not be found.', array('class' => $sClass, 'path' => $sPath));
				}
			}
			else {
				$this->getLogger()->error('The file {path} for component {class} could not be included.', array('class' => $sClass, 'path' => $sPath));
			}
		}
		else if($this->m_aClasses[$sClass] == $sPath) {
			$this->getLogger()->warning('The component {class} was allready registered.', array('class' => $sClass, 'path' => $sPath));
		}
		else {
			$this->getLogger()->error('A component {class} allready exists on a different path.', array('class' => $sClass, 'path1' => $sPath, 'path2' => $this->m_aData[$sClass]['path']));
		}
	}
	
	public final function getComponentName($sType) {
		if(isset($this->m_aLinkage[$sType])) {
			return array_first($this->m_aLinkage[$sType]);
		}
		return null;
	}
	
	public final function getComponnetNames($sType) {
		if(isset($this->m_aLinkage[$sType])) {
			return $this->m_aLinkage[$sType];
		}
		return array();
	}
	
	public final function getComponentInstance($sType, array $aParams = array()) {
		$sClass = $this->getComponentName($sType);
		if(!empty($sClass)) {
			$oClass = new ReflectionClass($sClass);
			return $oClass->newInstanceArgs($aParams);
		}
		return null;
	}
	
	public final function getComponnetInstances($sType, array $aParams = array()) {
		$aReturn = array();
		$aClasses = $this->getComponentName($sType);
		foreach($aClasses as $sClass) {
			$oClass = new ReflectionClass($sClass);
			$aReturn []= $oClass->newInstanceArgs($aParams);
		}
		return $aReturn;
	}
}
