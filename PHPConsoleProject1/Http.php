<?php

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

/**
 * Http short summary.
 *
 * Http description.
 *
 * @version 1.0
 * @author bradleysserbus
 */
class Http
{
    private $settings;
    private $appKey;
    private $client;

    const EXCEPTION_NAME = "exception";
    const RESULT_NAME = "result";

    public function __construct($settings, $appKey)
    {
        $this->settings = $settings;

        $this->appKey = $appKey;

        $this->client = new Client();
    }

    public function getAccessTokenString()
    {
        if ($this->settings->getAccessTokenString() == null)
        {
            $this->registerDevice();
        }

        return $this->settings->getAccessTokenString();
    }

    private function registerDevice()
    {
        $response = $this->handleDictionaryRequests('POST', "/devices", ['json' => [
            "appId" =>  $this->settings->getAppId(),
            "appKey" =>  $this->appKey,
            "platform" =>  PHP_OS,
            "model" =>  "",
            "osVersion" =>  "",
            "uniqueId" =>  $this->settings->getUniqueId(),
        ]]);

        if (!in_array(self::EXCEPTION_NAME, $response))
        {
            $this->settings->setDeviceToken($response[self::RESULT_NAME]);
        }
    }

    private function handleDictionaryRequests($verb, $path, $dictionary, $file = null)
    {
        $dictionary = $this->handleLastLocation($dictionary);

        $url = $this->getUrl($path);

        if ($file != null)
        {
            $dictionary['multipart'] = [['data' => ["data" => $file]]];
        }

        return $this->handleRequest($verb, $url, $dictionary);
    }

    private function getUrl($path)
    {
        $url = $this->settings->getServiceRoot();

        $url->setPath($path);

        return $url;
    }

    private function handleLastLocation($dictionary)
    {
        return $dictionary;
    }

    private function handleRequest($verb, $url, $dictionary)
    {
        $response = null;

        try
        {
            # TODO: turn on SSL validation
            $dictionary['verify'] = false;

            $response = $this->client->$verb($url, $dictionary);
        }
        catch (Exception $ex)
        {

        }

        return $response == null ? null : $response->json();
    }
}