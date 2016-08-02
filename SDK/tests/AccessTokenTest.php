<?php

namespace Buddy\Tests;

use Buddy\AccessToken;

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