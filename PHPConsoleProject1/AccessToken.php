<?php

class AccessToken
{
    private $token;
    private $expires;

    public function __construct($token, $expiresTicks)
    {
        $this->token = $token;
        $this->setExpires($expiresTicks);
    }

    public function getToken()
    {
        if ($this->token ==  null || $this->token == "" || $this->expires < new DateTime('now', new DateTimeZone('UTC')))
            return null;
        else
            return $this->token;
    }

    public function getExpires()
    {
        return $this->expires;
    }

    public function setExpires($ticks)
    {
        if ($ticks == null || $ticks == "")
            $this->expires = new DateTime('now', new DateTimeZone('UTC'));
        else
        {
            $timestamp = $ticks / 1000;
            $this->expires = new DateTime();
            $this->expires->setTimestamp($timestamp);
        }
    }
}

?>