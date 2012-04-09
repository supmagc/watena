<?php

class DbInvalidDbObjectData extends Exception {
	
	public function __construct(DbTable $oTable, $nId) {
		parent::__construct('Unable to create a data-object for ' . $oTable->getIdField() . ' == ' . $nId . ' from table `' . $oTable->getTable() . '`.');
	}
}

?>