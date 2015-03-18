<?php
require_plugin('UserManager');

class UserManagerTest extends Test {
	
	private $m_oUserVerified;
	private $m_oUserUnverified;
	
	public function setup() {
		UserManager::getDatabaseConnection()->query('DELETE FROM `user`');
		UserManager::getDatabaseConnection()->query('DELETE FROM `user_email`');
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
		$this->assertTrue($this->m_oUserVerified->setGender(null));
		$this->assertTrue($this->m_oUserVerified->setGender(''));
		$this->assertNull($this->m_oUserVerified->getGender());
		
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
		$this->assertTrue($this->m_oUserVerified->setLocale(null));
		$this->assertTrue($this->m_oUserVerified->setLocale(''));
		$this->assertNull($this->m_oUserVerified->getLocale());
		
		$this->assertTrue($this->m_oUserVerified->setLocale('nl'));
		$this->assertTrue($this->m_oUserVerified->setLocale('be_nl'));
		$this->assertTrue($this->m_oUserVerified->setLocale('NL'));
		$this->assertTrue($this->m_oUserVerified->setLocale('BE_NL'));
		$this->assertFalse($this->m_oUserVerified->setLocale('BE_NL-UTF8'));
	}

	public function testUserTimezone() {
		$this->assertTrue($this->m_oUserVerified->setTimezone(null));
		$this->assertTrue($this->m_oUserVerified->setTimezone(''));
		$this->assertNull($this->m_oUserVerified->getTimezone());
		
		$this->assertTrue($this->m_oUserVerified->setTimezone('UTC'));
		$this->assertTrue($this->m_oUserVerified->setTimezone('GMT+1'));
		$this->assertTrue($this->m_oUserVerified->setTimezone('Europe/Brussels'));
		$this->assertTrue($this->m_oUserVerified->setTimezone('Brussels'));
		$this->assertEquals('Europe/Brussels', $this->m_oUserVerified->getTimezone());
		$this->assertFalse($this->m_oUserVerified->setTimezone('toe-der-nie-toe'));
	}
	
	public function testUserBirthday() {
		$this->assertTrue($this->m_oUserVerified->setBirthday(null));
		$this->assertTrue($this->m_oUserVerified->setBirthday(''));
		$this->assertNull($this->m_oUserVerified->getBirthday());
		
		$this->assertTrue($this->m_oUserVerified->setBirthday('2004-12-31'));
		$this->assertTrue($this->m_oUserVerified->setBirthday('2004/12/31'));
		$this->assertTrue($this->m_oUserVerified->setBirthday('31-12-2004'));
		$this->assertTrue($this->m_oUserVerified->setBirthday('31/12/2004'));
		$this->assertEquals('2004-12-31', $this->m_oUserVerified->getBirthday());
		
		$this->assertFalse($this->m_oUserVerified->setBirthday('12/31/2004'));
		$this->assertFalse($this->m_oUserVerified->setBirthday('12th dec. 2004'));
	}

	public function testUserPassword() {
		$this->assertTrue($this->m_oUserVerified->setPassword(''));
		$this->assertTrue($this->m_oUserVerified->setPassword(null));
		$this->assertFalse($this->m_oUserVerified->verifyPassword(null));
		$this->assertFalse($this->m_oUserVerified->setPassword('a'));
		
		$sPassword = 'dxs  kul153\'(§è!çàm^mù ';
		$this->assertTrue($this->m_oUserVerified->setPassword($sPassword));
		$this->assertTrue($this->m_oUserVerified->verifyPassword($sPassword));
		$this->assertFalse($this->m_oUserVerified->verifyPassword('hnjlkkljg'));
		
		$this->assertEquals($this->m_oUserVerified->encodePassword('azerty'), $this->m_oUserVerified->encodePassword('azerty'));
		$this->assertNotEquals($this->m_oUserVerified->encodePassword('azerty'), $this->m_oUserVerified->encodePassword('azert'));
		$this->assertNotEquals($this->m_oUserVerified->encodePassword('azerty'), $this->m_oUserUnverified->encodePassword('azerty'));
	}
	
	public function testUserEmail() {
		$this->assertNull($this->m_oUserVerified->createEmail('bla@borabora'));
		$this->assertNull($this->m_oUserVerified->createEmail(null));
		$this->assertNull($this->m_oUserVerified->createEmail(''));
		
		$oEmailVerified = $this->m_oUserVerified->createEmail('jelle_verified@test.com', true);
		$this->assertNull($this->m_oUserVerified->createEmail('jelle_verified@test.com'));
		$oEmailUnverified = $this->m_oUserVerified->createEmail('jelle_unverified@test.com', false);
		$oEmailOtherUser = $this->m_oUserUnverified->createEmail('melissa_verified@test.com', true);
		$this->assertType('UserEmail', $oEmailVerified);
		$this->assertType('UserEmail', $oEmailUnverified);
		$this->assertType('UserEmail', $oEmailOtherUser);
		$this->assertTrue($oEmailVerified->getTime()->getTimestamp() - time() <= 1);
		$this->assertTrue($oEmailUnverified->getTime()->getTimestamp() - time() <= 1);
		$this->assertTrue($oEmailOtherUser->getTime()->getTimestamp() - time() <= 1);
		
		$this->assertFalse($this->m_oUserVerified->getContainerMails()->addItem($oEmailOtherUser));
		$this->assertFalse($this->m_oUserVerified->getContainerMails()->addItem($oEmailOtherUser));
		
		$this->assertEquals($oEmailVerified->getUserId(), UserManager::getUserIdByEmail($oEmailVerified->getEmail()));
		$this->assertEquals($oEmailUnverified->getUserId(), UserManager::getUserIdByEmail($oEmailUnverified->getEmail()));
		
		$this->assertFalse($oEmailUnverified->isVerified());
		$this->assertTrue($oEmailVerified->isVerified());
		$oEmailVerified->resetVerifier();
		$this->assertFalse($oEmailVerified->isVerified());
		$sVerifier = $oEmailVerified->makeVerifier();
		$this->assertTrue($oEmailVerified->verify($sVerifier));
		$this->assertTrue($oEmailVerified->isVerified());
		
		$aEmails = $this->m_oUserVerified->getContainerMails()->getItems();
		$this->assertEquals(2, count($aEmails));
		$this->assertTrue(isset($aEmails[Encoding::toLower($oEmailVerified->getEmail())]));
		$this->assertTrue(isset($aEmails[Encoding::toLower($oEmailUnverified->getEmail())]));
		$this->assertEquals($oEmailVerified, $aEmails[Encoding::toLower($oEmailVerified->getEmail())]);
		$this->assertEquals($oEmailUnverified, $aEmails[Encoding::toLower($oEmailUnverified->getEmail())]);
		
		$this->assertEquals($oEmailVerified, $this->m_oUserVerified->getContainerMails()->getItem(Encoding::toLower($oEmailVerified->getEmail())));
		$this->assertEquals($oEmailUnverified, $this->m_oUserVerified->getContainerMails()->getItem(Encoding::toLower($oEmailUnverified->getEmail())));
				
		$oEmailUnverified->delete();
		$this->m_oUserVerified->getContainerMails()->removeItem($oEmailVerified);
		$this->assertTrue($oEmailUnverified->isDeleted());
		$this->assertTrue($oEmailVerified->isDeleted());
		$aEmails = $this->m_oUserVerified->getContainerMails()->getItems();
		$this->assertEquals(0, count($aEmails));
	}
	
	// Test sessions
	public function testUserSession() {
		
	}
	
	// Test connections
	
	// Test groups
	
	// Test permissions
	
	// Test user membership
	
	// Test user allowances
			
	public function teardown() {
		$this->m_oUserVerified = null;
		$this->m_oUserUnverified = null;
	}
}
