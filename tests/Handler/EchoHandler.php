<?php
declare(strict_types=1);

namespace Itseasy\Websocket\Test\Handler;

use Amp\Delayed;
use Amp\Http\Server\HttpServer;
use Amp\Http\Server\Request;
use Amp\Http\Server\Response;
use Amp\Loop;
use Amp\Promise;
use Amp\Success;
use Amp\Websocket\Client;
use Amp\Websocket\Message;
use Amp\Websocket\Server\ClientHandler;
use Amp\Websocket\Server\Gateway;
use Amp\Websocket\Server\WebsocketServerObserver;
use DateTime;
use Generator;
use Laminas\Log\LoggerAwareInterface;
use Laminas\Log\LoggerAwareTrait;
use function Amp\call;

class EchoHandler implements ClientHandler, WebsocketServerObserver, LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected $wathcer;
    protected $data;

    public function onStart(HttpServer $server, Gateway $gateway): Promise
    {
        $this->watcher = Loop::repeat(1000, function () use ($gateway) {
            $this->data = (new DateTime())->format("Y-m-d H:i:s");
        });

        return new Success;
    }

    public function onStop(HttpServer $server, Gateway $gateway): Promise
    {
        Loop::cancel($this->watcher);
        return new Success;
    }

    public function handleHandshake(
        Gateway $gateway,
        Request $request,
        Response $response
    ) : Promise {
        return new Success($response);
    }

    public function handleClient(
        Gateway $gateway,
        Client $client,
        Request $request,
        Response $response
    ) : Promise {
        $jwt = $request->getAttribute("jwt");
        return call(function () use ($gateway, $client, $jwt): Generator {
            while ($message = yield $client->receive()) {
                if ($jwt->exp < time()) {
                    break;
                }

                $gateway->broadcast(sprintf(
                    yield $message->buffer()
                ));

                yield new Delayed(1000);
            }
        });

        // return call(function () use ($gateway, $client) : Generator {
        //     while (True) {
        //         $gateway->broadcast(json_encode([
        //             "data" => $this->data
        //         ]));
        //
        //         yield new Delayed(1000);
        //     };
        // });
    }
}
