<?php

class Object {
	
	/**
	 * This method provides the possibility to autodetermine required modules etc.
	 * The format is an associative array as needed by Context::checkRequirements(...)
	 */
	public static function getRequirements() {return null;}
	
	private static $s_oSingleton;
	
	protected function __construct() {
		if(get_class($this) == "Watena") self::$s_oSingleton = $this;
	}
	
	protected final function terminate($sMessage) {
		die($sMessage);
	}
	
	/**
	 * @return Watena
	 */
	public static final function getWatena() {
		return self::$s_oSingleton;
	}

	public function toString() {
		return get_class($this);
	}
	
	public final function __toString() {
		return $this->toString();
	}
}

?>