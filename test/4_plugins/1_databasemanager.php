<?php
require_plugin('DatabaseManager');

class DbObjectTest extends DbObject {
	
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
			'name_notnull' => 'r'.$this->m_sRandomName,
			'name_null' => null));
		$sInsert = $this->m_oTableSingle->insert(array(
			'name_notnull' => 'n'.$this->m_sRandomName,
			'name_null' => null), "name_notnull");
		
		$this->assertNumeric($nInsert);
		$this->assertEquals('n'.$this->m_sRandomName, $sInsert);
		
		$this->assertTrue($this->m_oTableSingle->update(array(
			'name_notnull' => 'rr'.$this->m_sRandomName
		), $nInsert));
		$this->assertTrue($this->m_oTableSingle->update(array(
			'name_null' => 'nn'.$this->m_sRandomName
		), $sInsert, 'name_notnull'));

		$aRow1 = $this->m_oTableSingle->select($nInsert)->fetch(PDO::FETCH_ASSOC);
		$aRow2 = $this->m_oTableSingle->select($sInsert, 'name_notnull')->fetch(PDO::FETCH_ASSOC);
		
		$this->assertEquals($nInsert, $aRow1['ID']);
		$this->assertEquals($sInsert, $aRow2['name_notnull']);
		$this->assertEquals('rr'.$this->m_sRandomName, $aRow1['name_notnull']);
		$this->assertEquals('nn'.$this->m_sRandomName, $aRow2['name_null']);
		$this->assertNull($aRow1['name_null']);
		
		$this->assertTrue($this->m_oTableSingle->delete($nInsert));
		$this->assertTrue($this->m_oTableSingle->delete($sInsert, 'name_notnull'));
	}
	
	public function testDbObjectTest() {
		$oInstance = DbObjectTest::createObject($this->m_oTableSingle, array(
			'name_notnull' => 'db'.$this->m_sRandomName,
			'name_null' => null
		));
		
		$oInstance->delete();
	}
	
	public function teardown() {
		$this->m_oConnection = null;
		$this->m_oTableSingle = null;
	}
}
