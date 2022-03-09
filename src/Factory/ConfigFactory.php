<?php
declare(strict_types=1);

namespace Itseasy\Websocket\Factory;

use Itseasy\Websocket\Config;
use Psr\Container\ContainerInterface;

class ConfigFactory
{
    public function __invoke(ContainerInterface $container) : Config
    {
        return new Config($container->get("Config")->getConfig()["websocket"]);
    }


}
