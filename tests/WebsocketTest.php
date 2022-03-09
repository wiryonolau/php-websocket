<?php
namespace Itseasy\Websocket\Test;

use Amp\Loop;
use Amp\PHPUnit\AsyncTestCase;
use Amp\Promise;
use Amp\Websocket\ClosedException;
use Amp\Websocket\Message;
use Exception;
use Itseasy\Websocket\Server;
use Firebase\JWT\JWT;

use function Amp\call;
use function Amp\Websocket\Client\connect;

final class WebsocketTest extends AsyncTestCase
{
    protected function startServer($app) : Promise
    {
        $server = $app->getContainer()->get(Server::class);
        $server = $server->prepare();

        return call(function() use ($server) {
            yield $server->start();
            return $server;
        });
    }

    public function testWebsocket()
    {
        $app = new Application([
            "config_path" => [
                __DIR__."/../config/*.config.php",
                __DIR__."/config/*.config.php"
            ],
        ]);
        $app->build();

        // Milisecond
        $this->setTimeout(2000);

        $time = time();

        $server = yield $this->startServer($app);

        $payload = [
            "iss" => "http://127.0.0.1:13370",
            "aud" => "http://127.0.0.1:13370",
            "iat" => $time,
            "nbf" => $time,
            "exp" => $time + 86400
        ];

        $token = JWT::encode(
            $payload,
            $app->getConfig()["websocket"]["guard"]["jwt"]["private_key"],
            $app->getConfig()["websocket"]["guard"]["jwt"]["headers"]["alg"]
        );

        try {
            $client = yield connect(sprintf('ws://127.0.0.1:13370/ws/echo?jwt=%s', $token));
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
