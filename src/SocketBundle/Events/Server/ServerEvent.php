<?php
namespace SocketBundle\Events\Server;

use SocketBundle\Events\Event;

class ServerEvent extends Event
{
    protected $baseCl;

    const SUCCESS_STATUS = 200;

    const TOPIC_MAIN = 'main';
    const TOPIC_QUEST = 'quest';

    const EVENT_REPLY = 'reply';
    const EVENT_ANSWER = 'answer';
    const EVENT_STATE_CONTENT = 'state_content';
    const EVENT_NEW_CONTENT = 'new_content';

    /**
     * @var string
     */
    protected $topic;
    /**
     * @var int
     */
    protected $status;

    /**
     * @var string
     */
    protected $response;

    /**
     * @return array
     */
    public function composeAnswer()
    {
        if (!$this->isAvailableTopic($this->topic)) {
            return [];
        }

        return [
            'topic' => $this->topic,
            'event' => $this->event,
            'payload' => $this->composePayload(),
            'pid' => $this->pid
        ];
    }

    /**
     * @return array
     */
    protected function composePayload()
    {
        return [
            'status' => $this->status,
            'response' => $this->response
        ];
    }

    /**
     * @param $topic
     * @return bool
     */
    protected function isAvailableTopic($topic)
    {
        return in_array($topic, [self::TOPIC_MAIN, self::TOPIC_QUEST]);
    }
}
