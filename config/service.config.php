<?php

namespace Itseasy\Websocket;

use DI;

return [
    "service" => [
        "factories" => [
            Config::class => Factory\ConfigFactory::class,
            Logger\DefaultLogger::class => Logger\Factory\DefaultLoggerFactory::class,
            Middleware\WebsocketGuardMiddleware::class => Middleware\Factory\WebsocketGuardMiddlewareFactory::class,
            Middleware\JwtGuardMiddleware::class => Middleware\Factory\JwtGuardMiddlewareFactory::class,
            Server::class => Factory\ServerFactory::class,
        ]
    ]
];
