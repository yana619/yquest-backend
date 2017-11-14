<?php
namespace SocketBundle\Events\Server;

use AppBundle\Utils\ResponseError;
use SocketBundle\Events\Client\ClientEvent;

class AnswerEvent extends ServerEvent
{
    /**
     * AnswerEvent constructor.
     *
     * @param ClientEvent $clientEvent
     * @param bool $isRightAnswer
     */
    public function __construct(ClientEvent $clientEvent, $isRightAnswer)
    {
        $this->topic = parent::TOPIC_QUEST;
        $this->event = parent::EVENT_ANSWER;

        $this->pid = $clientEvent->getPid();
        $this->status = $this->getStatus($isRightAnswer);
        $this->response = $this->getResponse();
    }

    /**
     * @param $isRightAnswer
     * @return int
     */
    private function getStatus($isRightAnswer)
    {
        return ($isRightAnswer)
            ? parent::SUCCESS_STATUS
            : ResponseError::UNKNOWN_ANSWER;
    }

    /**
     * @return string
     */
    private function getResponse()
    {
        return ($this->status != parent::SUCCESS_STATUS)
            ? ResponseError::description($this->status)
            : '';
    }
}
