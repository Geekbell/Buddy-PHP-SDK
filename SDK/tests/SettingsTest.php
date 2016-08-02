<?php

namespace Buddy\Tests;

use Buddy\Settings;

require_once 'vendor/autoload.php';

class SettingsTest extends \PHPUnit_Framework_TestCase
{
    const APP_ID = "ai";
    const APP_KEY = "ak";
    const DEFAULT_SERVICE_ROOT = "https://api.buddyplatform.com";
    const SERVICE_ROOT = "sr";
    const ACCESS_TOKEN = "at";

    public function setUp()
    {
        $config = new \Config_Lite(Settings::CFG_NAME);

        $config->clear();

        $config->save();
    }

    public function testGetDefaultServiceRoot()
	{
        $settings = new Settings(self::APP_ID, self::APP_KEY);

        $serviceRoot = $settings->getServiceRoot();

		$this->assertEquals(self::DEFAULT_SERVICE_ROOT, $serviceRoot);
	}

    public function testSettingsAccessToken()
	{
        $settings = new Settings(self::APP_ID, self::APP_KEY);

        $json = $this->getJson(1);

        $settings->setDeviceToken($json);

		$this->assertEquals($settings->getAccessTokenString(), SettingsTest::ACCESS_TOKEN);
	}

    private function getJson($days)
    {
        return ["accessToken" => self::ACCESS_TOKEN,
               "accessTokenExpires" => TestHelper::jsonExpiresTicksFromDays($days),
               "serviceRoot" => self::SERVICE_ROOT];
    }

    public function testSettingsAccessTokenExpired()
	{
        $settings = new Settings(self::APP_ID, self::APP_KEY);

        $json = $this->getJson(-1);

        $settings->setDeviceToken($json);

		$this->assertNull($settings->getAccessTokenString());
	}

    public function testSettingsSaveLoad()
	{
        $this->testSettingsAccessToken();

        $settings = new Settings(self::APP_ID, self::APP_KEY);

		$this->assertEquals($settings->getAccessTokenString(), self::ACCESS_TOKEN);
		$this->assertEquals($settings->getServiceRoot(), self::SERVICE_ROOT);
	}
}

?>