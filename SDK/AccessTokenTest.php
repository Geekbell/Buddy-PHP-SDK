<?php

spl_autoload_register(function ($class_name) {
    include $class_name . '.php';
});

class AccessTokenTest extends BaseTest
{
    public function testAccessToken()
	{
        $future = $this->getAccessToken(1);
		$this->assertEquals(self::ACCESS_TOKEN, $future->getToken());
	}

	public function testAccessTokenExpired()
	{
        $future = $this->getAccessToken(-1);
		$this->assertNull($future->getToken());
	}
}

?>