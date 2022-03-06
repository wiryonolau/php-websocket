<?php
declare(strict_types=1);

namespace Itseasy\Websocket\Console\Command;

use Exception;
use Itseasy\Websocket\Server;
use Laminas\Log\LoggerAwareInterface;
use Laminas\Log\LoggerAwareTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class WebsocketConsoleCommand extends Command implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected static $defaultName = "websocket";

    protected $server;

    public function __construct(Server $server)
    {
        parent::__construct();
        $this->server = $server;
    }

    public function execute(InputInterface $input, OutputInterface $output) : int
    {
        $this->server->start();

        return Command::SUCCESS;
    }
}
