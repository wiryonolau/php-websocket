<?php
declare(strict_types = 1);

namespace Itseasy\Websocket\Console\Command\Factory;

use Psr\Container\ContainerInterface;
use Itseasy\Websocket\Console\Command\WebsocketConsoleCommand;
use Itseasy\Websocket\Server;

class WebsocketConsoleCommandFactory
{
    public function __invoke(ContainerInterface $container) : WebsocketConsoleCommand
    {
        $server = $container->get(Server::class);
        return new WebsocketConsoleCommand($server);
    }
}
