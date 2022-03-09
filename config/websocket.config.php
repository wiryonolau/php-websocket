<?php

namespace Itseasy\Websocket;

return [
    "websocket" => [
        "listen_address" => [
            "0.0.0.0:13370"
        ],
        "allowed_origins" => [
            "http://localhost:8080",
        ],
        "handlers" => [
        ],
        "middlewares" => [
            Middleware\WebsocketGuardMiddleware::class
        ]
    ]
];
