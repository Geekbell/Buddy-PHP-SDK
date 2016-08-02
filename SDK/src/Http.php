<?php

namespace Buddy;

use GuzzleHttp\Client;
use GuzzleHttp\Exception;

require_once 'vendor/autoload.php';

class Http
{
    private $settings;
    private $client;

    private $lastLocation;

    const EXCEPTION_NAME = "exception";
    const RESULT_NAME = "result";

    public function __construct($settings)
    {
        $this->settings = $settings;

        $this->client = new \GuzzleHttp\Client();
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
        $url = $this->getUrl("/devices");

        $response = null;

        try {
            $response = $this->client->post($url, ['json' => [
                "appId" => $this->settings->getAppId(),
                "appKey" => $this->settings->getAppKey(),
                "platform" => PHP_OS,
                "model" => "",
                "osVersion" => "",
                "uniqueId" => $this->settings->getUniqueId(),
            ], "verify" => false]);
        }
        catch (Exception $ex)
        { }

        $response = $response->json();

        if (!in_array(self::EXCEPTION_NAME, $response))
        {
            $this->settings->setDeviceToken($response[self::RESULT_NAME]);
        }
    }

    private function handleDictionaryRequests($verb, $path, $dictionary, $file = null)
    {
        $dictionary = ['json' => $dictionary];

        if ($file != null)
        {
            # TODO: this needs to be verified
            throw new BadMethodCallException();
            $dictionary['multipart'] = [['data' => ["data" => $file]]];
        }

        return $this->handleRequest($verb, $path, $dictionary);
    }

    private function handleParametersRequests($verb, $path, $parameters)
    {
        $dictionary = ['query' => $parameters];

        return $this->handleRequest($verb, $path, $dictionary);
    }

    private function handleRequest($verb, $path, $dictionary)
    {
        $dictionary = $this->handleAuthentication($dictionary);

        $dictionary = $this->handleLastLocation($dictionary);

        $url = $this->getUrl($path);

        $response = null;

        try {
            # TODO: turn on SSL validation
            $dictionary['verify'] = false;

            $response = $this->client->$verb($url, $dictionary);
        }
        catch (Exception $ex) {
            $response = [EXCEPTION_NAME => $ex];
        }

        return $response == null ? [EXCEPTION_NAME => new \GuzzleHttp\Exception\TransferException()] : $response->json();
    }

    private function handleLastLocation($dictionary)
    {
        $lastLocation = $this->settings->getLastLocation();

        if ($lastLocation != null)
        {
            $dictionary["location"] = $lastLocation;
        }

        return $dictionary;
    }

    private function handleAuthentication($dictionary)
    {
        if ($this->settings->getAccessTokenString() == null)
        {
            $this->registerDevice();
        }

        $accessToken = $this->settings->getAccessTokenString();

        if ($accessToken != null)
        {
            $dictionary["headers"] = [ "Authorization" => "Buddy " . $accessToken];
        }

        return $dictionary;
    }

    private function getUrl($path)
    {
        $url = $this->settings->getServiceRoot();

        $url->setPath($path);

        return $url;
    }

    public function get($path, $parameters)
    {
        return $this->handleParametersRequests('get', $path, $parameters);
    }

    public function delete($path, $parameters)
    {
        return $this->handleParametersRequests('delete', $path, $parameters);
    }

    public function patch($path, $parameters)
    {
        return $this->handleDictionaryRequests('path', $path, $parameters);
    }

    public function post($path, $parameters)
    {
        return $this->handleDictionaryRequests('post', $path, $parameters);
    }

    public function put($path, $parameters)
    {
        return $this->handleDictionaryRequests('put', $path, $parameters);
    }

    public function createUser($userName, $password, $firstName=null, $lastName=null, $email=null, $gender=null, $dateOfBirth=null, $tag=null)
    {
        $response = $this->post("/users", [
            "username" => $userName,
            "password" => $password,
            "firstName" => $firstName,
            "lastName" => $lastName,
            "email" => $email,
            "gender" => $gender,
            "dateOfBirth"=> $dateOfBirth,
            "tag" =>$tag
        ]);

        if (!in_array(self::EXCEPTION_NAME, $response))
        {
            $this->settings->setUser($response[self::RESULT_NAME]);
        }

        return $response;
    }

    public function loginUser($userName, $password)
    {
        $response = $this->post("/users/login", [
            "username" => $userName,
            "password" => $password
        ]);

        if (!in_array(self::EXCEPTION_NAME, $response))
        {
            $this->settings->setUser($response[self::RESULT_NAME]);
        }

        return $response;
    }

    public function logoutUser()
    {
        $this->settings->setUser(null);
    }
}