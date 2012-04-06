<?php

abstract class UserConnectionProvider {
	
	abstract public function getConnectionId();
	
	abstract public function getConnectionData();
	
	abstract public function getConnectionTokens();

	abstract public function getConnectUrl();
	
	abstract public function isConnected();
	
	abstract public function disconnect();
	
	abstract public function connect();
}

?>