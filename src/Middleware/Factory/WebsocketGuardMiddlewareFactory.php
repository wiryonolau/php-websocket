<?php
declare(strict_types=1);

namespace Itseasy\Websocket\Middleware\Factory;

use Psr\Container\ContainerInterface;
use Itseasy\Websocket\Middleware\WebsocketGuardMiddleware;

class WebsocketGuardMiddlewareFactory
{
    public function __invoke(ContainerInterface $container) : WebsocketGuardMiddleware
    {
        $config = $container->get("Config")->getConfig()["websocket"];
        return new WebsocketGuardMiddleware($config);
    }
}
