<?php namespace Watena\Libs\Base;

class DbInvalidDbObjectId extends WatCeption {

	public function __construct(DbTable $oTable, $mId = null) {
		parent::__construct('Unable to find a valid ID \'{ID}\' for table `{table}`.`{field}`.', array(
			'ID' => $mId,
			'table' => $oTable->getTable(),
			'field' => $oTable->getIdField()
		));
	}
}

class DbInvalidDbMultiObjectId extends Exception {

	public function __construct(DbMultiTable $oTable, $sIdField, $mId = null) {
		parent::__construct('Unable to find a valid ID \'{ID}\' for table `{table}`.`{field}`.', array(
			'ID' => $mId,
			'table' => $oTable->getTable(),
			'field' => $sIdField
		));
	}
}

?>