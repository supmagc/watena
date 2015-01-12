<?php

abstract class ObjectUnique extends Object implements Serializable {

	private $m_mGroup;
	private $m_mId;
	
	private static $s_aInstances = array();
	
	protected final function __construct($mId, array $aParameters) {
		call_user_func_array(array($this, 'init'), $aParameters);
		self::setUniqueInstance($mId, $this);
	}
	
	public final function __sleep() {
		throw new ObjectUniquenessException(get_class($this), 'You can\'t serialize an instance of ObjectUnique.');
	}
	
	public final function __wakeup() {
		throw new ObjectUniquenessException(get_class($this), 'You can\'t unserialize an instance of ObjectUnique.');
	}
	
	public final function __clone() {
		throw new ObjectUniquenessException(get_class($this), 'You can\'t clone an instance of ObjectUnique.');
	}

	public final function serialize() {
		throw new ObjectUniquenessException(get_class($this), 'You can\'t serialize an instance of ObjectUnique.');
	}
	
	public final function unserialize($sSerialized) {
		throw new ObjectUniquenessException(get_class($this), 'You can\'t unserialize an instance of ObjectUnique.');
	}
	
	public final static function setUniqueInstance($mId, ObjectUnique $oInstance) {
		array_assure(self::$s_aInstances, array(get_called_class(), $mId), $oInstance);
	}
	
	public final static function getUniqueInstance($mId) {
		return array_value(self::$s_aInstances, array(get_called_class(), $mId));
	}
	
	public final static function listUniqueInstances() {
		return array_value(self::$s_aInstances, array(get_called_class()), array());
	}
	
	public final static function assureUniqueInstance($mId, array $aParameters) {
		$sClass = get_called_class();
		$oInstance = self::getUniqueInstance($mId);
		
		if($oInstance) {
			return $oInstance;
		}

		$oClass = new ReflectionClass($sClass);
		if(!$oClass->isSubclassOf('ObjectUnique')) {
			throw new ObjectUniquenessException($sClass, 'Requested class does not inherit from ObjectUnique.');
		}
				
		return new static($mId, $aParameters);
	}
}
