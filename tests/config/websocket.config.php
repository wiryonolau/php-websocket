<?php

namespace Itseasy\Websocket\Test;

return [
    "websocket" => [
        "handlers" => [
            [
                "route" => "/ws/echo",
                "method" => "GET",
                "handler" => Handler\EchoHandler::class
            ]
        ],
    ]
];
