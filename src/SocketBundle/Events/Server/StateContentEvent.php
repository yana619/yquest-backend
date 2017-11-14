<?php
namespace SocketBundle\Events\Server;

class StateContentEvent extends ServerEvent
{
    /**
     * StateContentEvent constructor.
     *
     * @param $content
     */
    public function __construct($content)
    {
        $this->topic = parent::TOPIC_QUEST;
        $this->event = parent::EVENT_STATE_CONTENT;

        $this->pid = null;
        $this->status = parent::SUCCESS_STATUS;
        $this->response = [
            'content' => $content
        ];
    }
}
