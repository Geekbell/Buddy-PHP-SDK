<?php

namespace Buddy\Tests;

use Buddy\Http;
use Buddy\Settings;

require_once 'vendor/autoload.php';

class HttpTest extends \PHPUnit_Framework_TestCase
{
    const US_APP_ID = "bbbbbc.swCBKKNNvBrkc";
    const US_APP_KEY = "667c8432-f3d6-6b8a-a660-855f5807f830";
    const EU_APP_ID = "<Your EU App ID>";
    const EU_APP_KEY = "<Your EU App Key>";

    const DEFAULT_SERVICE_ROOT = "https://api.buddyplatform.com";

    public function setUp()
    {
        $config = new \Config_Lite(Settings::CFG_NAME);
        $config->clear();
        $config->save();
    }

    public function testGetDeviceAccessTokenString()
	{
        $http = new Http(new Settings(self::US_APP_ID), self::US_APP_KEY);

        $deviceAccessTokenString = $http->getAccessTokenString();

        $this->assertNotNull($deviceAccessTokenString);
	}


    public function testLoginLogoutUser()
	{
        $http = new Http(new Settings(self::US_APP_ID), self::US_APP_KEY);

        $deviceAccessTokenString = $http->getAccessTokenString();
        $this->assertNotNull($deviceAccessTokenString);

        $userResponse = $http->loginUser("test", "12341234");
        $this->assertNotNull($userResponse);
        $userAccessTokenString = $http->getAccessTokenString();
        $this->assertTrue($deviceAccessTokenString != $userAccessTokenString);

        $http->logoutUser();
        $deviceAccessTokenString2 = $http->getAccessTokenString();
        $this->assertEquals($deviceAccessTokenString, $deviceAccessTokenString2);
	}
}
?>