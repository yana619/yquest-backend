<?php
namespace SocketBundle\Command;

use Ratchet\Http\HttpServer;
use Ratchet\Http\Router;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use SocketBundle\Server\SocketWorker;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class ServerCommand extends ContainerAwareCommand
{
    /**
     * Configure
     */
    protected function configure()
    {
        $this
            ->setName('quest:socket:server')
            ->setDescription('Start the socket server.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $collection = new RouteCollection;
        $handler = new SocketWorker($this->getContainer());

        $collection->add('v1', new Route('/v1', array(
            '_controller' => new WsServer(
                $handler
            ),
            'allowedOrigins' => '*'
        )));

        $router = new Router(new UrlMatcher($collection,
            new RequestContext()
        ));

        $server = IoServer::factory(
            new HttpServer($router),
            $this->getContainer()->getParameter('socket_port')
        );

        // TODO: Rewrite
        // Keep mysql connect alive
        $server->loop->addPeriodicTimer(15, function () use ($handler) {
            $handler->getContainer()->get('ws.ping_db')->ping();
        });

        $server->run();
    }
}
