<?php

class BaseTest extends PHPUnit_Framework_TestCase
{
    const APP_ID = "a";
    const DEFAULT_SERVICE_ROOT = "https://api.buddyplatform.com";
    const SERVICE_ROOT = "sr";
    const ACCESS_TOKEN = "at";

    protected function getAccessToken($days)
    {
        $datetime = $this->datetimeFromDays($days);

        $ticks = $this->ticksFromDatetime($datetime);

        $at = new AccessToken(self::ACCESS_TOKEN, "$ticks");

        return $at;
    }

    private function datetimeFromDays($days)
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

    protected function jsonTicksFromDatetime($datetime)
    {
        $ticks = $this->ticksFromDatetime($datetime);

        return "/Date(" . $ticks . ")/";
    }

    protected function jsonExpiresTicksFromDays($days)
    {
        return $this->jsonTicksFromDatetime($this->getAccessToken($days)->getExpires());
    }
}

?>