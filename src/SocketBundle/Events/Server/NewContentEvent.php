<?php
namespace SocketBundle\Events\Server;

class NewContentEvent extends ServerEvent
{
    /**
     * NewContentEvent constructor.
     *
     * @param $content
     */
    public function __construct($content)
    {
        $this->topic = parent::TOPIC_QUEST;
        $this->event = parent::EVENT_NEW_CONTENT;

        $this->pid = null;
        $this->status = parent::SUCCESS_STATUS;
        $this->response = [
            'content' => $content
        ];
    }
}
