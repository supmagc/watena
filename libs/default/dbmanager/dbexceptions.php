<?php

class DbInvalidDbObjectData extends Exception {
	
	public function __construct(DbTable $oTable, $mId) {
		parent::__construct('Unable to create a data-object for ' . $oTable->getIdField() . ' == ' . $mId . ' from table `' . $oTable->getTable() . '`.');
	}
}

class DbInvalidDbObjectId extends Exception {

	public function __construct(DbTable $oTable, $mId) {
		parent::__construct('Unable to find a valid ID ' . $mId . ' for table `' . $oTable->getTable() . '`.');
	}
}

?>