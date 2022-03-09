<?php
declare(strict_types=1);

namespace Itseasy\Websocket\Middleware\Factory;

use Itseasy\Websocket\Config;
use Itseasy\Websocket\Middleware\WebsocketGuardMiddleware;
use Psr\Container\ContainerInterface;

class WebsocketGuardMiddlewareFactory
{
    public function __invoke(ContainerInterface $container) : WebsocketGuardMiddleware
    {
        $config = $container->get(Config::class);
        return new WebsocketGuardMiddleware($config);
    }
}
