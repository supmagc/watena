<?php 
class TimeTest extends Test {
	
	public function testCreateUtcTime() {
		$oTime = Time::createUtcTime('now');
		$this->assertTrue($oTime->getTimestamp() - time() <= 1);
	}
}
?>