<?php
namespace SocketBundle\Events\Server;

use AppBundle\Utils\ResponseError;
use SocketBundle\Events\Client\ClientEvent;

class HintEvent extends ServerEvent
{
    /**
     * HintEvent constructor.
     *
     * @param ClientEvent $clientEvent
     * @param string $response
     */
    public function __construct(ClientEvent $clientEvent, $response = '')
    {
        $this->topic = parent::TOPIC_QUEST;
        $this->event = parent::EVENT_HINT;

        $this->pid = $clientEvent->getPid();
        $this->status = $status = parent::SUCCESS_STATUS;
        $this->response = $response;
    }
}
