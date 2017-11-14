<?php
namespace SocketBundle\Events\Client;

use Ratchet\ConnectionInterface;
use SocketBundle\Events\Event;

class ClientEvent extends Event
{
    /**
     * @var ConnectionInterface
     */
    private $client;

    /**
     * ClientEvent constructor.
     * @param ConnectionInterface $from
     * @param $eventData
     */
    public function __construct(ConnectionInterface $from, $eventData)
    {
        $this->client = $from;
        $this->parseEventData($eventData);
    }

    /**
     * @return ConnectionInterface
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param $eventData
     */
    private function parseEventData($eventData)
    {
        $eventData = $this->jsonToArray($eventData);

        $this->event = $eventData['event'] ?? null;
        $this->pid = $eventData['pid'] ?? null;
        $this->payload = $eventData['payload'] ?? [];
    }

    /**
     * @param $json
     * @return array
     */
    private function jsonToArray($json)
    {
        $array = @json_decode($json, true);

        if ($array && is_array($array)) {
            return $array;
        }

        return [$json];
    }
}
