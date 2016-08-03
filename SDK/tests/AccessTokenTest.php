<?php

namespace Buddy\Tests;

require_once "vendor/autoload.php";

class AccessTokenTest extends \PHPUnit_Framework_TestCase
{
    public function testAccessToken()
	{
        $future = TestHelper::getAccessToken(1);
		$this->assertEquals(TestHelper::ACCESS_TOKEN, $future->getToken());
	}

	public function testAccessTokenExpired()
	{
        $future = TestHelper::getAccessToken(-1);
		$this->assertNull($future->getToken());
	}
}

?>