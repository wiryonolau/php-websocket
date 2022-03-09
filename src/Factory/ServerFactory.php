<?php
declare(strict_types=1);

namespace Itseasy\Websocket\Factory;

use Amp\Websocket\Server\Websocket as AmpWebsocket;
use Itseasy\Websocket\Config;
use Itseasy\Websocket\Logger\DefaultLogger;
use Itseasy\Websocket\Server;
use Itseasy\Websocket\WebsocketHandler;
use Laminas\Log\LoggerAwareInterface;
use Psr\Container\ContainerInterface;

class ServerFactory
{
    public function __invoke(ContainerInterface $container) : Server
    {
        $config = $container->get(Config::class);

        if ($container->has("Logger")) {
            $logger = $container->get("Logger");
        } else {
            $logger = $container->get(DefaultLogger::class);
        }

        $middlewares = [];
        foreach ($config->getMiddlewares() as $middleware) {
            $globalMiddleware = $container->get($middleware);
            if ($globalMiddleware instanceof LoggerAwareInterface) {
                $globalMiddleware->setLogger($logger);
            }
            $middlewares[] = $globalMiddleware;
        }

        $handlers = [];


        foreach ($config->getHandlers() as $handler) {
            $routeMiddleware = isset($handler["middlewares"]) ? $handler["middlewares"] : [];

            $route = new WebsocketHandler(
                $handler["route"],
                $handler["method"],
                $container->get($handler["handler"]),
                array_map(function($middleware) use ($container) {
                    return $container->get($middleware);
                }, $routeMiddleware)
            );

            $route->setLogger($logger);
            $handlers[] = $route;
        }

        $server = new Server($config, $handlers, $middlewares);
        $server->setLogger($logger);

        return $server;
    }


}
