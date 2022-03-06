<?php

namespace Itseasy\Websocket\Test;

use DI;

return [
    "service" => [
        "factories" => [
            Handler\EchoHandler::class => DI\create()
        ]
    ]
];
