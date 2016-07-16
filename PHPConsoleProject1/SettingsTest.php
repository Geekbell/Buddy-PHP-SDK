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

class SettingsTest extends PHPUnit_Framework_TestCase
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
        $settings = new Settings(SettingsTest::APP_ID);

		$this->assertEquals(self::DEFAULT_SERVICE_ROOT, $settings->getServiceRoot());
	}

    /*
     *     def test_Settings_access_token(self):
        settings = Settings(Test3._app_id)

        json = {"accessToken": Test3._access_token,
                "accessTokenExpires": self.future_javascript_access_token_expires(),
                "serviceRoot": Test3._service_root}

        settings.set_device_token(json)

        self.assertEqual(settings.access_token_string, Test3._access_token)

    def test_Settings_access_token_expired(self):
        settings = Settings(Test3._app_id)

        json = {"accessToken": Test3._access_token,
                "accessTokenExpires": self.past_javascript_access_token_expires(),
                "serviceRoot": Test3._service_root}

        settings.set_device_token(json)

        self.assertEqual(settings.access_token_string, None)

    def test_Settings_save_load(self):

        # pre-load Settings with a different test
        self.test_Settings_access_token()

        settings = Settings(Test3._app_id)

        self.assertEqual(settings.access_token_string, Test3._access_token)
        self.assertEqual(settings.service_root, Test3._service_root)
        */
}

?>