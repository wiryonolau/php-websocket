<?php

namespace Itseasy\Websocket;

use DI;

return [
    "service" => [
        "factories" => [
            Logger\DefaultLogger::class => Logger\Factory\DefaultLoggerFactory::class,
            Middleware\WebsocketGuardMiddleware::class => Middleware\Factory\WebsocketGuardMiddlewareFactory::class,
            Server::class => Factory\ServerFactory::class,
        ]
    ]
];
