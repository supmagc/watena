<?php

class Object {
	
	private static $s_oSingleton;
	
	protected function __construct() {
		if(get_class($this) == "Watena") self::$s_oSingleton = $this;
	}
	
	public function terminate($sMessage) {
		die($sMessage);
	}

	public final function getWatena() {
		return self::$s_oSingleton;
	}
}

?>