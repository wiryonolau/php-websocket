<?php
namespace Itseasy\Websocket\Test;

use Amp\Loop;
use Amp\PHPUnit\AsyncTestCase;
use Amp\Promise;
use Amp\Websocket\ClosedException;
use Amp\Websocket\Message;
use Exception;
use Itseasy\Websocket\Server;

use function Amp\delay;
use function Amp\call;
use function Amp\Websocket\Client\connect;

final class WebsocketTest extends AsyncTestCase
{
    protected function startServer() : Promise
    {
        $app = new Application([
            "config_path" => [
                __DIR__."/../config/*.config.php",
                __DIR__."/config/*.config.php"
            ],
        ]);
        $app->build();

        $server = $app->getContainer()->get(Server::class);
        $server = $server->prepare();

        return call(function() use ($server) {
            yield $server->start();
            return $server;
        });
    }

    public function testWebsocket()
    {
        $this->setTimeout(200);

        $server = yield $this->startServer();

        try {
            $client = yield connect('ws://127.0.0.1:13370/ws/echo');
            $client->send("Test");
            $message = yield $client->receive();

            $this->assertInstanceOf(Message::class, $message);
            $this->assertFalse($message->isBinary());
            $this->assertSame('Test', yield $message->buffer());

            $promise = $client->receive();
            $client->close();

            $this->assertNull(yield $promise);
        } catch (Exception $e) {
            debug($e->getMessage());
        } finally {
            $server->stop();
        }
    }
}
