<?php

require __DIR__ . '/vendor/autoload.php';

class Settings
{
    const CFG_NAME = "buddy.cfg";
    const SERVICE_ROOT_NAME = "service_root";
    const DEVICE_TOKEN_NAME = "device";
    const USER_TOKEN_NAME = "user";
    const USER_ID_NAME = "user_id";
    const UNIQUE_ID_NAME = "unique_id";
    const DEFAULT_SERVICE_ROOT = "https://api.buddyplatform.com";
    const ACCESS_TOKEN_NAME_SUFFIX = "_access_token";
    const ACCESS_TOKEN_EXPIRES_NAME_SUFFIX = "_access_token_expires";

    public function __construct($appId)
    {
        $this->appId = $appId;

        $this->config = new Config_Lite(Settings::CFG_NAME);
    }

    public function getServiceRoot()
    {
        $serviceRoot  = $this->config->get($this->appId, Settings::SERVICE_ROOT_NAME, Settings::DEFAULT_SERVICE_ROOT);

        return $serviceRoot;
    }
}

?>