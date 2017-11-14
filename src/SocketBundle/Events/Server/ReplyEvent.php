<?php
namespace SocketBundle\Events\Server;

use SocketBundle\Events\Client\ClientEvent;

class ReplyEvent extends ServerEvent
{
    /**
     * ReplyEvent constructor.
     *
     * @param ClientEvent $clientEvent
     * @param int $status
     * @param string $response
     */
    public function __construct(ClientEvent $clientEvent, $status = parent::SUCCESS_STATUS, $response = '')
    {
        $this->topic = parent::TOPIC_MAIN;
        $this->event = parent::EVENT_REPLY;

        $this->pid = $clientEvent->getPid();
        $this->status = $status;
        $this->response = $response;
    }
}
