<?php

namespace Itseasy\Websocket;

use DI;

return [
    "service" => [
        "factories" => [
            Logger\DefaultLogger::class => Logger\Factory\DefaultLoggerFactory::class,
            Middleware\WebsocketGuardMiddlewareFactory::class => Middleware\Factory\WebsocketGuardMiddlewareFactory::class,
            Server::class => Factory\ServerFactory::class,
        ]
    ]
];
