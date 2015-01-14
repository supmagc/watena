<?php
require_plugin('DatabaseManager');

class DbObjectTest extends DbObject {
	
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
}

class DatabaseManagerTest extends Test {
	
	private $m_oConnection;
	private $m_oTableSingle;
	private $m_sRandomName;
	
	public function setup() {
		$this->m_oConnection = DatabaseManager::getConnection('testing');
		$this->m_oTableSingle = $this->m_oConnection->getTable('table_single', 'ID');
		$this->m_sRandomName = md5(time());
	}
	
	public function testConnection() {
		$this->assertType('DbConnection', $this->m_oConnection);
		$this->assertEquals('testing', $this->m_oConnection->getUser());
		$this->assertEquals('testing', $this->m_oConnection->getPass());
		$this->assertEquals('testing', Encoding::toLower($this->m_oConnection->getIdentifier()));
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
			'name_notnull' => 'osnn'.$this->m_sRandomName,
			'name_null' => null));
		$sInsert = $this->m_oTableSingle->insert(array(
			'name_notnull' => 'onnn'.$this->m_sRandomName,
			'name_null' => null), "name_notnull");
		
		$this->assertNumeric($nInsert);
		$this->assertEquals('onnn'.$this->m_sRandomName, $sInsert);
		
		$this->assertTrue($this->m_oTableSingle->update(array(
			'name_notnull' => 'connn'.$this->m_sRandomName
		), $nInsert));
		$this->assertTrue($this->m_oTableSingle->update(array(
			'name_null' => 'cosn'.$this->m_sRandomName
		), $sInsert, 'name_notnull'));

		$aRow1 = $this->m_oTableSingle->select($nInsert)->fetch(PDO::FETCH_ASSOC);
		$aRow2 = $this->m_oTableSingle->select($sInsert, 'name_notnull')->fetch(PDO::FETCH_ASSOC);
		
		$this->assertEquals($nInsert, $aRow1['ID']);
		$this->assertEquals($sInsert, $aRow2['name_notnull']);
		$this->assertEquals('connn'.$this->m_sRandomName, $aRow1['name_notnull']);
		$this->assertEquals('cosn'.$this->m_sRandomName, $aRow2['name_null']);
		$this->assertNull($aRow1['name_null']);
		
		$this->assertTrue($this->m_oTableSingle->delete($nInsert));
		$this->assertTrue($this->m_oTableSingle->delete($sInsert, 'name_notnull'));
	}
	
	public function testDbObjectTest() {
		$oInstance = DbObjectTest::createObject($this->m_oTableSingle, array(
			'name_notnull' => 'dbnn'.$this->m_sRandomName,
			'name_null' => null
		));

		$this->assertNull($oInstance->getNameNull());
		$oInstance->setNameNotNull('cdbnn'.$this->m_sRandomName);
		$oInstance->setNameNull('cdbn'.$this->m_sRandomName);
		
		$oStatement = $this->m_oConnection->select('table_single', $oInstance->getId(), 'ID');
		$aRow = $oStatement->fetch(PDO::FETCH_ASSOC);
		$this->assertEquals('cdbn'.$this->m_sRandomName, $aRow['name_null']);
		$this->assertEquals('cdbn'.$this->m_sRandomName, $oInstance->getNameNull());
		$this->assertEquals('cdbnn'.$this->m_sRandomName, $aRow['name_notnull']);
		$this->assertEquals('cdbnn'.$this->m_sRandomName, $oInstance->getNameNotNull());
		
		$this->assertEquals($oStatement->rowCount(), 1);
		
		$this->assertFalse($oInstance->isDeleted());
		$oInstance->delete();
		$this->assertTrue($oInstance->isDeleted());
	}
	
	public function teardown() {
		$this->m_oConnection = null;
		$this->m_oTableSingle = null;
	}
}
