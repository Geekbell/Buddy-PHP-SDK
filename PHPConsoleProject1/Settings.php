<?php

require __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Url;

class Settings
{
    const CFG_NAME = "buddy.cfg";
    const SERVICE_ROOT_NAME = "service_root";
    const UNIQUE_ID_NAME = "unique_id";
    const DEVICE_TOKEN_NAME = "device";
    const USER_TOKEN_NAME = "user";
    const USER_ID_NAME = "user_id";
    const USER_ID_JSON_NAME = "id";
    const DEFAULT_SERVICE_ROOT = "https://api.buddyplatform.com";
    const ACCESS_TOKEN_NAME_SUFFIX = "_access_token";
    const ACCESS_TOKEN_EXPIRES_NAME_SUFFIX = "_access_token_expires";
    const SERVICE_ROOT_JSON_NAME = "serviceRoot";
    const ACCESS_TOKEN_JSON_NAME = "accessToken";
    const ACCESS_TOKEN_EXPIRES_JSON_NAME = "accessTokenExpires";

    private $appId;
    private $config;

    public function __construct($appId)
    {
        $this->appId = $appId;

        $this->config = new Config_Lite(self::CFG_NAME);
    }

    public function getUniqueId()
    {
        $uniqueId  = $this->get(self::UNIQUE_ID_NAME);

        if ($uniqueId == null)
        {
            $uniqueId = uniqid("", true);

            $this->set(self::UNIQUE_ID_NAME, $uniqueId);
        }

        return $uniqueId;
    }

    public function getAppId()
    {
        return $this->appId;
    }

    public function getServiceRoot()
    {
        $serviceRoot  = $this->get(self::SERVICE_ROOT_NAME);

        if ($serviceRoot == null)
        {
            $serviceRoot = self::DEFAULT_SERVICE_ROOT;
        }

        return Url::fromString($serviceRoot);
    }

    public function getDeviceToken()
    {
        return $this->getAccessToken(self::DEVICE_TOKEN_NAME);
    }

    public function getUserToken()
    {
        return $this->getAccessToken(self::USER_TOKEN_NAME);
    }

    private function getAccessToken($settingsTokenName)
    {
        $token = $this->get($settingsTokenName . self::ACCESS_TOKEN_NAME_SUFFIX);

        if ($token == null)
        {
            return null;
        }
        else
        {
            return new AccessToken($token,
                $this->get($settingsTokenName . self::ACCESS_TOKEN_EXPIRES_NAME_SUFFIX));
        }
    }

    public function getAccessTokenString()
    {
        $token = $this->getUserToken();

        if ($token == null)
        {
            $token = $this->getDeviceToken();
        }

        if ($token == null)
        {
            return null;
        }

        return $token->getToken();
    }

    public function setDeviceToken($response)
    {
        $this->setAccessToken(self::DEVICE_TOKEN_NAME, $response);

        if ($response == null || !array_key_exists(self::SERVICE_ROOT_JSON_NAME, $response))
        {
            $this->remove(self::SERVICE_ROOT_NAME);
        }
        else
        {
            $this->set(self::SERVICE_ROOT_NAME, $response[self::SERVICE_ROOT_JSON_NAME]);
        }

        $this->save();
    }

    public function setUser($response)
    {
        $this->setAccessToken(self::USER_TOKEN_NAME, $response);

        if ($response == null)
        {
            $this->remove(self::USER_ID_NAME);
        }
        else
        {
            $this->set(self::USER_ID_NAME, $response[self::USER_ID_JSON_NAME]);
        }

        $this->save();
    }

    public function setAccessToken($settingsTokenName, $result)
    {
        if ($result == null)
        {
            $this->remove($settingsTokenName . self::ACCESS_TOKEN_NAME_SUFFIX);
            $this->remove($settingsTokenName . self::ACCESS_TOKEN_EXPIRES_NAME_SUFFIX);
        }
        else
        {
            $this->set($settingsTokenName . self::ACCESS_TOKEN_NAME_SUFFIX, $result[self::ACCESS_TOKEN_JSON_NAME]);
            $this->set($settingsTokenName . self::ACCESS_TOKEN_EXPIRES_NAME_SUFFIX,
                       $this->ticksFromJavaScriptDatetime($result[self::ACCESS_TOKEN_EXPIRES_JSON_NAME]));
        }
    }

    private function ticksFromJavaScriptDatetime($dt)
    {
        $output = [];

        $matchFound = preg_match("/\/Date\((\d+)\)\//", $dt, $output);

        return $output[1];
    }

    public function set($name, $value)
    {
        $this->config->set($this->appId, $name, $value);
    }

    public function get($name)
    {
        if ($this->config->has($this->appId, $name))
        {
            return $this->config->get($this->appId, $name);
        }
        else
        {
            return null;
        }
    }

    public function remove($name)
    {
        if ($this->config->has($this->appId, $name))
        {
            $this->config->remove($this->appId, $name);
        }
    }

    public function save()
    {
        $this->config->save();
    }
}

?>