<?php

namespace SocketBundle\Events;

class Event
{
    /**
     * @var string
     */
    protected $event;

    /**
     * @var array
     */
    protected $payload;

    /**
     * @var string
     */
    protected $pid;

    /**
     * @return string
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @return string
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * @return array
     */
    public function getPayload()
    {
        return $this->payload;
    }
}
