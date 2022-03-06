<?php
declare(strict_types=1);

namespace Itseasy\Websocket\Logger\Factory;

use Psr\Container\ContainerInterface;
use Laminas\Log\Logger;

class DefaultLoggerFactory
{
    public function __invoke(ContainerInterface $container) : Logger
    {
        $logger = new Logger();
        $logger->addWriter('stream', null, ['stream' => 'php://stderr']);
        return $logger;
    }
}
