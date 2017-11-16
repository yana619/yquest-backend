<?php
namespace SocketBundle\Server;

use AppBundle\Entity\User;
use AppBundle\Utils\ResponseError;
use SocketBundle\Events\Client\ClientEvent;
use SocketBundle\Events\Server\AnswerEvent;
use SocketBundle\Events\Server\HintEvent;
use SocketBundle\Events\Server\NewContentEvent;
use SocketBundle\Events\Server\ReplyEvent;
use SocketBundle\Events\Server\ServerEvent;
use SocketBundle\Events\Server\StateContentEvent;

class EventManager
{
    /**
     * @var SocketWorker
     */
    protected $connManager;

    /**
     * @var ClientEvent
     */
    private $clientEvent;

    /**
     * EventManager constructor.
     * @param SocketWorker $connManager
     */
    public function __construct(SocketWorker $connManager)
    {
        $this->connManager = $connManager;
    }

    /**
     * Mapping ClientEvent -> Server Event
     * @param ClientEvent $clientEvent
     */
    public function mapping(ClientEvent $clientEvent)
    {
        $this->clientEvent = $clientEvent;

        switch ($this->clientEvent->getEvent()) {
            case 'join':
                $this->onJoin();
                break;

            case 'ping':
                $this->onPing();
                break;

            case 'answer':
                $this->onAnswer();
                break;

            case 'hint':
                $this->onHint();
                break;

            default:
                $this->error(ResponseError::UNKNOWN_EVENT);
        }
    }

    /**
     * Answer on Join Event
     */
    private function onJoin()
    {
        $payload = $this->clientEvent->getPayload();

        if ($this->authUserByToken($payload['token'] ?? null)) {
            $this->send($this->replyEvent());

            $this->send(
                (new StateContentEvent(
                    $this->getService('quest.quest_manager')->getStateData($this->getUserId())
                ))
            );
        }
    }

    /**
     * Answer on Ping Event
     */
    private function onPing()
    {
        $this->send($this->replyEvent());
    }

    /**
     * Answer on Answer Event
     */
    private function onAnswer()
    {
        if (!$this->checkAuth()) {
            return;
        }

        $payload = $this->clientEvent->getPayload();

        $checkAnswer = $this->getService('quest.quest_manager')->checkAnswer(
            $this->getUserId(),
            $payload['chapterId'] ?? null,
            $payload['answer'] ?? null
        );

        $this->send(new AnswerEvent($this->clientEvent, $checkAnswer));

        if ($checkAnswer) {
            $newData = $this
                ->getService('quest.quest_manager')
                ->getNewData($payload['chapterId'], $this->getUserId(), true);

            if ($newData) {
                $this->send(
                    new NewContentEvent($newData)
                );
            }

        }
    }

    /**
     * Answer on Hint Event
     */
    private function onHint()
    {
        if (!$this->checkAuth()) {
            return;
        }

        $payload = $this->clientEvent->getPayload();

        $hint = $this->getService('quest.quest_manager')->getHint(
            $this->getUserId(),
            $payload['chapterId'] ?? null
        );

        if ($hint) {
            $this->send(new HintEvent($this->clientEvent, $hint));
        }
    }


    /**
     * Compose ReplyEvent
     *
     * @param int $status
     * @param string $response
     * @return ReplyEvent
     */
    private function replyEvent($status = 200, $response = '')
    {
        return new ReplyEvent($this->clientEvent, $status, $response);
    }

    /**
     * Error Event
     * @param $errorCode
     */
    private function error($errorCode)
    {
        $this->send(
            $this->replyEvent($errorCode, ResponseError::description($errorCode))
        );
    }

    /**
     * @param ServerEvent $event
     */
    private function send($event)
    {
        $message = json_encode($event->composeAnswer());

        $this->clientEvent
            ->getClient()
            ->send($message);
    }

    /**
     * @param $serviceName
     * @return object
     */
    private function getService($serviceName)
    {
        return $this->connManager->getContainer()->get($serviceName);
    }

    /**
     * @return bool
     */
    private function checkAuth()
    {
        if ($this->getUserId()) {
            return true;
        }

        $this->error(ResponseError::WRONG_AUTH_TOKEN);

        return false;
    }

    /**
     * @param $token
     * @return bool
     */
    private function authUserByToken($token)
    {
        /** @var User $user */
        $user = $this->getService('app.session_provider')->loadUserByToken($token);

        if ($user) {
            $this->clientEvent->getClient()->userId = $user->getId();

            return true;
        }

        $this->error(ResponseError::WRONG_AUTH_TOKEN);

        return false;
    }

    /**
     * @return int|null;
     */
    private function getUserId()
    {
        return $this->clientEvent->getClient()->userId;
    }
}
