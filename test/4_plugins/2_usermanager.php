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
		$this->assertTrue($this->m_oUserVerified->isVerified());
		$this->m_oUserVerified->resetVerifier();
		$this->assertFalse($this->m_oUserVerified->isVerified());
		$sVerifier = $this->m_oUserVerified->makeVerifier();
		$this->assertTrue($this->m_oUserVerified->verify($sVerifier));
		$this->assertTrue($this->m_oUserVerified->isVerified());
	}
	
	public function testUserFisrtLastName() {
		$this->assertTrue($this->m_oUserVerified->setFirstName(' Jelle'));
		$this->assertTrue($this->m_oUserVerified->setLastName('Van Der Voet En Der Voet En Der Voet En Der Voet En Der Voet En Der Voet '));
		$this->assertEquals('Jelle', $this->m_oUserVerified->getFirstname());
		$this->assertEquals('Van Der Voet En Der Voet En Der Voet En Der Voet En Der Voet En', $this->m_oUserVerified->getLastname());
	}
	
	public function testUserGender() {
		$this->assertTrue($this->m_oUserVerified->setGender('m'));
		$this->assertEquals('male', $this->m_oUserVerified->getGender());
		$this->assertTrue($this->m_oUserVerified->setGender('M'));
		$this->assertEquals('male', $this->m_oUserVerified->getGender());
		$this->assertTrue($this->m_oUserVerified->setGender('f'));
		$this->assertEquals('female', $this->m_oUserVerified->getGender());
		$this->assertTrue($this->m_oUserVerified->setGender('F'));
		$this->assertEquals('female', $this->m_oUserVerified->getGender());
		$this->assertTrue($this->m_oUserVerified->setGender('male'));
		$this->assertEquals('male', $this->m_oUserVerified->getGender());
		$this->assertTrue($this->m_oUserVerified->setGender('female'));
		$this->assertEquals('female', $this->m_oUserVerified->getGender());
		$this->assertFalse($this->m_oUserVerified->setGender('random'));
		$this->assertEquals('female', $this->m_oUserVerified->getGender());
		$this->assertTrue($this->m_oUserVerified->setGender(null));
		$this->assertNull($this->m_oUserVerified->getGender());
	}

	public function testUserName() {
		$this->assertTrue($this->m_oUserVerified->setName('Jelle'));
		$this->assertFalse($this->m_oUserVerified->setName(null));
		$this->assertFalse($this->m_oUserUnverified->setName('Jelle'));
		$this->assertFalse($this->m_oUserUnverified->setName('jelle'));
		$this->assertFalse($this->m_oUserUnverified->setName(' jelle 123 $^µù'));
		$this->assertFalse($this->m_oUserUnverified->setName('mj'));
		$this->assertTrue($this->m_oUserUnverified->setName('Melissa'));
		$this->assertEquals('Jelle', $this->m_oUserVerified->getName());
		$this->assertEquals('Melissa', $this->m_oUserUnverified->getName());
	}

	public function testUserLocale() {
	
	}

	public function testUserTimezone() {
	
	}
	
	public function testUserBirthday() {
	
	}

	public function testUserPassword() {
	
	}
			
	public function teardown() {
		$this->m_oUserVerified = null;
		$this->m_oUserUnverified = null;
	}
}
