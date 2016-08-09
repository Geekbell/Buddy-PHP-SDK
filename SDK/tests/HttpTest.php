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
        $http = new Http(new Settings(self::US_APP_ID, self::US_APP_KEY));

        $deviceAccessTokenString = $http->getAccessTokenString();

        $this->assertNotNull($deviceAccessTokenString);
	}

    public function testLoginLogoutUser()
	{
	    $settings = new Settings(self::US_APP_ID, self::US_APP_KEY);
        $http = new Http($settings);

        $deviceAccessTokenString = $http->getAccessTokenString();
        $this->assertNotNull($deviceAccessTokenString);
        $userToken = $settings->getUserToken();
        $this->assertNull($userToken);

        $userResponse = $http->loginUser("test", "12341234");
        $this->assertNotNull($userResponse);
        $this->assertStatusOK($userResponse);
        $userAccessTokenString = $http->getAccessTokenString();
        $this->assertTrue($deviceAccessTokenString != $userAccessTokenString);
        $userToken = $settings->getUserToken();
        $this->assertNotNull($userToken);

        $http->logoutUser();
        $deviceAccessTokenString2 = $http->getAccessTokenString();
        $this->assertEquals($deviceAccessTokenString, $deviceAccessTokenString2);
        $userToken = $settings->getUserToken();
        $this->assertNull($userToken);
	}

    public function testGet()
    {
        $http = new Http(new Settings(self::US_APP_ID, self::US_APP_KEY));

        $userResponse = $http->loginUser("test", "12341234");
        $this->assertStatusOK($userResponse);

        $getResponse = $http->get("/users", []);
        $this->assertStatusOK($getResponse);
        $this->assertGreaterThanOrEqual(1, count($getResponse["result"]["pageResults"]));
    }

    public function testPutPost()
    {
        $http = new Http(new Settings(self::US_APP_ID, self::US_APP_KEY));

        $userResponse = $http->loginUser("test", "12341234");
        $this->assertStatusOK($userResponse);

        $key = uniqid();

        $putResponse = $http->put("/metadata/me/" . $key, ["value" => 1, "visibility" => "User"]);
        $this->assertStatusCreated($putResponse);

        $postResponse = $http->post("/metadata/me/" . $key . "/increment", ["delta" => 2, "visibility" => "User"]);
        $this->assertStatusOK($postResponse);

        $getResponse = $http->get("/metadata/me/" . $key, ["visibility" => "User"]);
        $this->assertStatusOK($getResponse);

        $this->assertEquals(3, $getResponse["result"]["value"]);
    }

    private function assertStatusOK($response)
    {
        $this->assertEquals(200, $response["status"]);
    }

    private function assertStatusCreated($response)
    {
        $this->assertEquals(201, $response["status"]);
    }
}
?>