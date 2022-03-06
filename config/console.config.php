<?php

namespace Itseasy\Websocket\Console\Command;

use DI;

return [
    "console" => [
        "commands" => [
            WebsocketConsoleCommand::class
        ],
        "factories" => [
            WebsocketConsoleCommand::class => Factory\WebsocketConsoleCommandFactory::class
        ]
    ]
];
