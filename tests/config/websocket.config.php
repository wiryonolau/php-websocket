<?php

namespace Itseasy\Websocket\Test;

return [
    "websocket" => [
        "allowed_origins" => [
            "http://127.0.0.1:13370",
        ],
        "handlers" => [
            [
                "route" => "/ws/echo",
                "method" => "GET",
                "handler" => Handler\EchoHandler::class
            ]
        ],
    ]
];
