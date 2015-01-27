<?php
require_plugin('UserManager');

class UserManagerTest extends Test {
	
	private $m_oUserVerified;
	private $m_oUserUnverified;
	
	public function setup() {
		UserManager::getDatabaseConnection()->query('DELETE FROM `user`');
	}
	
	public function testUserCreation() {
		$this->m_oUserVerified = User::create('UserVerified', true);
		$this->m_oUserUnverified = User::create('UserUnverified', false);
		
		$this->assertEquals('UserVerified', $this->m_oUserVerified->getName());
		$this->assertEquals('UserUnverified', $this->m_oUserUnverified->getName());
		$this->assertTrue($this->m_oUserVerified->isVerified());
		$this->assertFalse($this->m_oUserUnverified->isVerified());
	}
	
	public function testUserLoading() {
		$this->assertEquals($this->m_oUserVerified, User::load($this->m_oUserVerified->getId()));
		$this->assertEquals($this->m_oUserUnverified, User::load($this->m_oUserUnverified->getId()));
	}
	
	public function testUserVerifier() {
		$this->m_oUserVerified->resetVerifier();
		/*$this->assertFalse($this->m_oUserVerified->isVerified());
		$sVerifier = $this->m_oUserVerified->makeVerifier();
		$this->assertTrue($this->m_oUserVerified->verify($sVerifier));
		$this->assertTrue($this->m_oUserVerified->isVerified());*/
	}
	
	public function teardown() {
		$this->m_oUserVerified = null;
		$this->m_oUserUnverified = null;
	}
}
