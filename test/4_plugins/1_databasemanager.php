<?php
require_plugin('DatabaseManager');

class DbObjectSingleTest extends DbObject {
	
	public function getNameNull() {
		return $this->getDataValue('name_null');
	}
	
	public function setNameNull($sName) {
		return $this->setDataValue('name_null', $sName);
	}
	
	public function getNameNotNull() {
		return $this->getDataValue('name_notnull');
	}
	
	public function setNameNotNull($sName) {
		return $this->setDataValue('name_notnull', $sName);
	}
	
	public function setId($nId) {
		return $this->setDataValue('ID', $nId);
	}
}

class DbObjectMultiTest extends DbMultiObject {

	public function getNameNull() {
		return $this->getDataValue('name_null');
	}

	public function setNameNull($sName) {
		return $this->setDataValue('name_null', $sName);
	}

	public function getNameNotNull() {
		return $this->getDataValue('name_notnull');
	}

	public function setNameNotNull($sName) {
		return $this->setDataValue('name_notnull', $sName);
	}
	
	public function getIdA() {
		return $this->getDataValue('ID_A');
	}

	public function setIdA($nId) {
		return $this->setDataValue('ID_A', $nId);
	}
	
	public function getIdB() {
		return $this->getDataValue('ID_B');
	}
	
	public function setIdB($nId) {
		return $this->setDataValue('ID_B', $nId);
	}
}

class DatabaseManagerTest extends Test {
	
	private $m_oConnection;
	private $m_oTableSingle;
	private $m_oTableMulti;
	private $m_sRandomName;
	
	public function setup() {
		$this->m_oConnection = DatabaseManager::getConnection('testing');
		$this->m_oTableSingle = $this->m_oConnection->getTable('table_single', 'ID');
		$this->m_oTableMulti = $this->m_oConnection->getMultiTable('table_multi', array('ID_A', 'ID_B'));
		$this->m_sRandomName = md5(time());
	}
	
	public function testConnection() {
		$this->assertType('DbConnection', $this->m_oConnection);
		$this->assertEquals('testing', $this->m_oConnection->getUser());
		$this->assertEquals('testing', $this->m_oConnection->getPass());
		$this->assertEquals('testing', Encoding::toLower($this->m_oConnection->getIdentifier()));
	}

	public function testConnectionOperations() {
		$this->assertType('PDOStatement', $this->m_oConnection->query('DELETE FROM `table_single`'));
		$this->assertType('PDOStatement', $this->m_oConnection->query('DELETE FROM `table_multi`'));
	}
	
	public function testConnectionConnect() {
		$this->assertTrue($this->m_oConnection->isConnected());
		$this->m_oConnection->disconnect();
		$this->assertFalse($this->m_oConnection->isConnected());
		$this->m_oConnection->connect();
		$this->assertTrue($this->m_oConnection->isConnected());
	}
	
	public function testTableSingle() {
		$this->assertType('DbTable', $this->m_oTableSingle);
		$this->assertEquals($this->m_oConnection->getIdentifier(), $this->m_oTableSingle->getConnection()->getIdentifier());
		$this->assertEquals('table_single', $this->m_oTableSingle->getTable());
		$this->assertEquals('ID', $this->m_oTableSingle->getIdField());
		$this->assertEquals($this->m_oTableSingle, $this->m_oConnection->getTable('table_single', 'ID'));
	}
	
	public function testTableSingleOperations() {
		$nInsert = $this->m_oTableSingle->insert(array(
			'name_notnull' => 'onnn'.$this->m_sRandomName,
			'name_null' => 'onn'.$this->m_sRandomName));
		$sInsert = $this->m_oTableSingle->insert(array(
			'name_notnull' => 'osnn'.$this->m_sRandomName,
			'name_null' => null), "name_notnull");
		
		$this->assertNumeric($nInsert);
		$this->assertEquals('osnn'.$this->m_sRandomName, $sInsert);
		
		$this->assertTrue($this->m_oTableSingle->update(array(
			'name_null' => null
		), $nInsert));
		$this->assertTrue($this->m_oTableSingle->update(array(
			'name_null' => 'cosn'.$this->m_sRandomName
		), $sInsert, 'name_notnull'));

		$aRow1 = $this->m_oTableSingle->select($nInsert)->fetch(PDO::FETCH_ASSOC);
		$aRow2 = $this->m_oTableSingle->select($sInsert, 'name_notnull')->fetch(PDO::FETCH_ASSOC);
		
		$this->assertEquals($nInsert, $aRow1['ID']);
		$this->assertEquals($sInsert, $aRow2['name_notnull']);
		$this->assertEquals('cosn'.$this->m_sRandomName, $aRow2['name_null']);
		$this->assertNull($aRow1['name_null']);
		
		$this->assertTrue($this->m_oTableSingle->delete($nInsert));
		$this->assertTrue($this->m_oTableSingle->delete($sInsert, 'name_notnull'));
	}
	
	public function testObjectSingleTest() {
		$oInstance = DbObjectSingleTest::createObject($this->m_oTableSingle, array(
			'ID' => 99,
			'name_notnull' => 'dbnn'.$this->m_sRandomName,
			'name_null' => null
		));

		$this->assertEquals(99, $oInstance->getId());
		$this->assertEquals('dbnn'.$this->m_sRandomName, $oInstance->getNameNotNull());
		$this->assertNull($oInstance->getNameNull());
		
		$this->assertEquals($oInstance, DbObjectSingleTest::loadObject($this->m_oTableSingle, 99));
		
		$oInstance->setId(101);
		$oInstance->setNameNotNull('cdbnn'.$this->m_sRandomName);
		$oInstance->setNameNull('cdbn'.$this->m_sRandomName);
		
		$oStatement = $this->m_oConnection->select('table_single', $oInstance->getId(), 'ID');
		$aRow = $oStatement->fetch(PDO::FETCH_ASSOC);
		$this->assertEquals($oStatement->rowCount(), 1);
		
		$this->assertEquals(101, $aRow['ID']);
		$this->assertEquals(101, $oInstance->getId());
		$this->assertEquals('cdbn'.$this->m_sRandomName, $aRow['name_null']);
		$this->assertEquals('cdbn'.$this->m_sRandomName, $oInstance->getNameNull());
		$this->assertEquals('cdbnn'.$this->m_sRandomName, $aRow['name_notnull']);
		$this->assertEquals('cdbnn'.$this->m_sRandomName, $oInstance->getNameNotNull());
		
		$this->assertFalse($oInstance->isDeleted());
		$oInstance->delete();
		$this->assertTrue($oInstance->isDeleted());
	}

	public function testObjectSingleFailedUpdate() {
		$oInstance = DbObjectSingleTest::createObject($this->m_oTableSingle, array(
			'name_notnull' => 'dbnn'.$this->m_sRandomName,
			'name_null' => null
		));
		$this->m_oConnection->query('DELETE FROM `table_single` WHERE `ID` = :id', array('id' => $oInstance->getId()));
		$this->assertNull($oInstance->getNameNull());
		$oInstance->setNameNull('cdbn'.$this->m_sRandomName);
		$this->assertNull($oInstance->getNameNull());
	}

	public function testObjectSingleLoadList() {
	
	}
	
	public function testTableMulti() {
		$this->assertType('DbMultiTable', $this->m_oTableMulti);
		$this->assertEquals($this->m_oConnection->getIdentifier(), $this->m_oTableMulti->getConnection()->getIdentifier());
		$this->assertEquals('table_multi', $this->m_oTableMulti->getTable());
		$this->assertEquals(array('ID_A', 'ID_B'), $this->m_oTableMulti->getIdFields());
		$this->assertEquals($this->m_oTableMulti, $this->m_oConnection->getMultiTable('table_multi', array('ID_A', 'ID_B')));
	}

	public function testTableMultiOperations() {
		$aInsert1 = $this->m_oTableMulti->insert(array(
				'ID_A' => 1,
				'ID_B' => 1,
				'name_notnull' => 'o1nn'.$this->m_sRandomName,
				'name_null' => null));
		$aInsert2 = $this->m_oTableMulti->insert(array(
				'ID_A' => 1,
				'ID_B' => 2,
				'name_notnull' => 'o2nn'.$this->m_sRandomName,
				'name_null' => null));
		$aInsert3 = $this->m_oTableMulti->insert(array(
				'ID_A' => 2,
				'ID_B' => 1,
				'name_notnull' => 'o3nn'.$this->m_sRandomName,
				'name_null' => null));
		
		$this->assertEquals(array(1, 1), $aInsert1);
		$this->assertEquals(array(1, 2), $aInsert2);
		$this->assertEquals(array(2, 1), $aInsert3);
	
		$this->assertTrue($this->m_oTableMulti->update(array(
				'ID_B' => 3
		), $aInsert1));
		$this->assertTrue($this->m_oTableMulti->update(array(
				'name_notnull' => 'co2nn'.$this->m_sRandomName
		), $aInsert2));
		$this->assertTrue($this->m_oTableMulti->update(array(
				'name_null' => 'co3n'.$this->m_sRandomName
		), $aInsert3));
	
		$aRow1 = $this->m_oTableMulti->select(array(1, 3))->fetch(PDO::FETCH_ASSOC);
		$aRow2 = $this->m_oTableMulti->select($aInsert2)->fetch(PDO::FETCH_ASSOC);
		$aRow3 = $this->m_oTableMulti->select(array(2, 'co3n'.$this->m_sRandomName), array('ID_A', 'name_null'))->fetch(PDO::FETCH_ASSOC);
	
		$this->assertEquals(3, $aRow1['ID_B']);
		$this->assertEquals('co2nn'.$this->m_sRandomName, $aRow2['name_notnull']);
		$this->assertEquals('co3n'.$this->m_sRandomName, $aRow3['name_null']);
		$this->assertNull($aRow1['name_null']);
		$this->assertNull($aRow2['name_null']);
		
		$this->assertTrue($this->m_oTableMulti->delete(array(1, 3)));
		$this->assertTrue($this->m_oTableMulti->delete($aInsert2));
		$this->assertTrue($this->m_oTableMulti->delete(array(2, 'co3n'.$this->m_sRandomName), array('ID_A', 'name_null')));
	}
	
	public function testObjectMultiOperations() {
		$oInstance = DbObjectMultiTest::createObject($this->m_oTableMulti, array(
			'ID_A' => 1,
			'ID_B' => 1,
			'name_notnull' => 'dbnn'.$this->m_sRandomName,
			'name_null' => null
		));

		$this->assertEquals(1, $oInstance->getIdA());
		$this->assertEquals(1, $oInstance->getIdB());
		$this->assertEquals('dbnn'.$this->m_sRandomName, $oInstance->getNameNotNull());
		$this->assertNull($oInstance->getNameNull());
		
		$this->assertEquals($oInstance, DbObjectMultiTest::loadObject($this->m_oTableMulti, array(1, 1)));
		
		$oInstance->setIdA(2);
		$oInstance->setIdB(2);
		$oInstance->setNameNotNull('cdbnn'.$this->m_sRandomName);
		$oInstance->setNameNull('cdbn'.$this->m_sRandomName);
		
		$oStatement = $this->m_oConnection->select('table_multi', array(2, 2), array('ID_A', 'ID_B'));
		$aRow = $oStatement->fetch(PDO::FETCH_ASSOC);
		$this->assertEquals($oStatement->rowCount(), 1);
		
		$this->assertEquals(2, $aRow['ID_A']);
		$this->assertEquals(2, $aRow['ID_B']);
		$this->assertEquals(2, $oInstance->getIdA());
		$this->assertEquals(2, $oInstance->getIdB());
		$this->assertEquals('cdbn'.$this->m_sRandomName, $aRow['name_null']);
		$this->assertEquals('cdbn'.$this->m_sRandomName, $oInstance->getNameNull());
		$this->assertEquals('cdbnn'.$this->m_sRandomName, $aRow['name_notnull']);
		$this->assertEquals('cdbnn'.$this->m_sRandomName, $oInstance->getNameNotNull());
		
		$this->assertFalse($oInstance->isDeleted());
		$oInstance->delete();
		$this->assertTrue($oInstance->isDeleted());
	}
	
	public function testObjectMultiFailedUpdate() {
		$oInstance = DbObjectMultiTest::createObject($this->m_oTableMulti, array(
			'ID_A' => 1,
			'ID_B' => 1,
			'name_notnull' => 'dbnn'.$this->m_sRandomName,
			'name_null' => null
		));
		$this->m_oConnection->query('DELETE FROM `table_multi` WHERE `ID_A` = 1 AND `ID_B` = 1');
		$oInstance->setNameNull('cdbn'.$this->m_sRandomName);
		$this->assertNull($oInstance->getNameNull());
		$this->assertTrue($oInstance->isDeleted());
	}

	public function testObjectMultiLoadList() {
	
	}
	
	public function teardown() {
		$this->m_oConnection = null;
		$this->m_oTableSingle = null;
		$this->m_oTableMulti = null;
	}
}
