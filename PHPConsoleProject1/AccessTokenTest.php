<?php

spl_autoload_register(function ($class_name) {
    include $class_name . '.php';
});

class AccessTokenTest extends PHPUnit_Framework_TestCase
{
    const APP_ID = "a";
    const DEFAULT_SERVICE_ROOT = "https://api.buddyplatform.com";
    const SERVICE_ROOT = "sr";
    const ACCESS_TOKEN = "at";

    protected function datetimeFromDays($days)
    {
        $interval = DateInterval::createFromDateString($days." days");
        $datetime = new DateTime('now', new DateTimeZone('UTC'));
        $datetime->add($interval);
        return $datetime;
    }

    protected function ticksFromDatetime($datetime)
    {
        $timestamp = $datetime->getTimestamp();
        return $timestamp * 1000;
    }

    private function getAccessToken($days)
    {
        $datetime = $this->datetimeFromDays($days);
        $ticks = $this->ticksFromDatetime($datetime);
        $at = new AccessToken(self::ACCESS_TOKEN, "$ticks");

        return $at;
    }

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