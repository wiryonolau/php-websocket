<?php
declare(strict_types=1);

namespace Itseasy\Websocket\Middleware\Factory;

use Psr\Container\ContainerInterface;
use Itseasy\Websocket\Config;
use Itseasy\Websocket\Middleware\JwtGuardMiddleware;

class JwtGuardMiddlewareFactory
{
    public function __invoke(ContainerInterface $container) : JwtGuardMiddleware
    {
        $config = $container->get(Config::class);
        return new JwtGuardMiddleware($config);
    }
}
