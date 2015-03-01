<?php

/*
class ComponentLoader extends CacheableFile {
	
	private $m_sClass;
	private $m_aExtends = array();
	private $m_aImplements = array();
	
	public final function make(array $aMembers) {
		$this->includeFile();
		if(!class_exists($this->m_sClass)) {
			throw new WatCeption('Unable to leave this componentLoader in serializeable state since it\'s component class {class} could not be found.', array('class' => $this->m_sClass, 'filename' => $this->getFileName(), 'filepath' => $this->getFilePath()));
		}
		else {
			$this->m_aExtends = class_parents($this->m_sClass);
			$this->m_aImplements = class_implements($this->m_sClass);
		}
	}
	
	public final function getClass() {
		return $this->m_sClass;
	}
	
	public final function getExtends() {
		return $this->m_aExtends;
	}
	
	public final function getImplements() {
		return $this->m_aImplements;
	}
	
	public final function createInstance(array $aArguments = array()) {
		if($this->includeFile() && class_exists($this->m_sClass)) {
			$oType = new ReflectionClass($this->m_sClass);
			return $oType->newInstanceArgs($aArguments);
		}
		else {
			$this->getLogger()->error('Unable to create an instance of the required component since the class {class} couldn\'t be found.', array('class' => $this->m_sClass, 'filename' => $this->getFileName(), 'filepath' => $this->getFilePath()));
			return null;
		}
	}
	
	public static final function create($sClass, $sPath) {
		return CacheableFile::create($sPath, array('m_sClass' => $sClass));
	}
}
*/
