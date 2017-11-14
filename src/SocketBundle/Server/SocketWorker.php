<?php

namespace SocketBundle\Server;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use SocketBundle\Events\Client\ClientEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use SplObjectStorage;

class SocketWorker implements MessageComponentInterface
{
    /**
     * @var SplObjectStorage
     */
    protected $clients;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var EventManager
     */
    protected $eventManager;

    /**
     * SocketWorker constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->clients = new SplObjectStorage;
        $this->container = $container;
        $this->eventManager = new EventManager($this);
    }

    /**
     * A new websocket connection
     *
     * @param ConnectionInterface $conn
     */
    public function onOpen(ConnectionInterface $conn)
    {
        $conn->userId = null;
        $this->clients->attach($conn);
    }

    /**
     * Handle message sending
     *
     * @param ConnectionInterface $from
     * @param string $msg
     */
    public function onMessage(ConnectionInterface $from, $msg)
    {
        $this->eventManager->mapping(
            new ClientEvent($from, $msg)
        );
    }

    /**
     * A connection is closed
     * @param ConnectionInterface $conn
     */
    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
    }

    /**
     * Error handling
     *
     * @param ConnectionInterface $conn
     * @param \Exception $e
     */
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $conn->close();
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }
}
