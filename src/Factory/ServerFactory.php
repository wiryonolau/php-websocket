<?php
declare(strict_types=1);

namespace Itseasy\Websocket\Factory;

use Amp\Websocket\Server\Websocket as AmpWebsocket;
use Itseasy\Websocket\Logger\DefaultLogger;
use Itseasy\Websocket\Server;
use Psr\Container\ContainerInterface;
use Laminas\Log\LoggerAwareInterface;

class ServerFactory
{
    public function __invoke(ContainerInterface $container) : Server
    {
        $config = $container->get("Config")->getConfig()["websocket"];

        if ($container->has("Logger")) {
            $logger = $container->get("Logger");
        } else {
            $logger = $container->get(DefaultLogger::class);
        }

        $middlewares = [];
        foreach ($config["middlewares"] as $middleware) {
            $globalMiddleware = $container->get($middleware);
            $globalMiddleware = $this->setObjectLogger($globalMiddleware, $logger);
            $middlewares[] = $globalMiddleware;
        }

        foreach ($config["handlers"] as $key => $handler) {
            $actualHandler = $container->get($handler["handler"]);
            $actualHandler = $this->setObjectLogger($actualHandler, $logger);

            $config["handlers"][$key]["handler"] = $actualHandler;

            if (!empty($handler["middlewares"])) {
                foreach ($handler["middlewares"] as $index => $middleware) {
                    $actualMiddleware = $container->get($middleware);
                    $actualMiddleware = $this->setObjectLogger($actualMiddleware, $logger);
                    $config["handlers"][$key]["middlewares"][$index] = $actualMiddleware;
                }
            } else {
                $config["handlers"][$key]["middlewares"] = [];
            }
        }

        $server = new Server($config, $middlewares);
        $server->setLogger($logger);

        return $server;
    }

    private function setObjectLogger($obj, $logger)
    {
        try {
            if ($obj instanceof LoggerAwareInterface) {
                $obj->setLogger($logger);
            }
        } catch (Exception $e) {
            $logger->debug($e->getMessage());
        }
        return $obj;
    }
}
