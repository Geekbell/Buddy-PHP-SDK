<?php

namespace Buddy\Tests;

use Buddy\AccessToken;

require_once 'vendor/autoload.php';

class TestHelper
{
    const APP_ID = "a";
    const DEFAULT_SERVICE_ROOT = "https://api.buddyplatform.com";
    const SERVICE_ROOT = "sr";
    const ACCESS_TOKEN = "at";

    public static function getAccessToken($days)
    {
        $datetime = TestHelper::datetimeFromDays($days);

        $ticks = TestHelper::ticksFromDatetime($datetime);

        $at = new AccessToken(self::ACCESS_TOKEN, "$ticks");

        return $at;
    }

    private static function datetimeFromDays($days)
    {
        $interval = \DateInterval::createFromDateString($days." days");
        $datetime = new \DateTime('now', new \DateTimeZone('UTC'));
        $datetime->add($interval);
        return $datetime;
    }

    public static function ticksFromDatetime($datetime)
    {
        $timestamp = $datetime->getTimestamp();
        return $timestamp * 1000;
    }

    public static function jsonTicksFromDatetime($datetime)
    {
        $ticks = TestHelper::ticksFromDatetime($datetime);

        return "/Date(" . $ticks . ")/";
    }

    public static function jsonExpiresTicksFromDays($days)
    {
        return TestHelper::jsonTicksFromDatetime(TestHelper::getAccessToken($days)->getExpires());
    }
}

?>