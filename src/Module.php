<?php
declare(strict_types=1);

namespace Itseasy\Websocket;

class Module
{
    public static function getConfigPath() : array
    {
        return [
            __DIR__."/../config/*.{local,config}.php"
        ];
    }
}
