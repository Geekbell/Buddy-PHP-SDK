<?php

spl_autoload_register(function ($class_name) {
    if ($class_name == "Config_Lite")
    {
        include 'Config\Lite.php';
    }
    else
    {
        include $class_name . '.php';
    }
});

set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . "\\vendor\\pear\\config_lite");

class SettingsTest extends BaseTest
{
    const APP_ID = "a";
    const DEFAULT_SERVICE_ROOT = "https://api.buddyplatform.com";
    const SERVICE_ROOT = "sr";
    const ACCESS_TOKEN = "at";

    public function setUp()
    {
        $config = new Config_Lite(Settings::CFG_NAME);

        $config->clear();

        $config->save();
    }

    public function testGetDefaultServiceRoot()
	{
        $settings = new Settings(self::APP_ID);

		$this->assertEquals(self::DEFAULT_SERVICE_ROOT, $settings->getServiceRoot());
	}

    public function testSettingsAccessToken()
	{
        $settings = new Settings(self::APP_ID);

        $json = $this->getJson(1);

        $settings->setDeviceToken($json);

		$this->assertEquals($settings->getAccessTokenString(), SettingsTest::ACCESS_TOKEN);
	}

    private function getJson($days)
    {
        return ["accessToken" => self::ACCESS_TOKEN,
               "accessTokenExpires" => $this->jsonExpiresTicksFromDays($days),
               "serviceRoot" => self::SERVICE_ROOT];
    }

    public function testSettingsAccessTokenExpired()
	{
        $settings = new Settings(self::APP_ID);

        $json = $this->getJson(-1);

        $settings->setDeviceToken($json);

		$this->assertNull($settings->getAccessTokenString());
	}

    public function testSettingsSaveLoad()
	{
        $this->testSettingsAccessToken();

        $settings = new Settings(self::APP_ID);

		$this->assertEquals($settings->getAccessTokenString(), self::ACCESS_TOKEN);
		$this->assertEquals($settings->getServiceRoot(), self::SERVICE_ROOT);
	}
}

?>