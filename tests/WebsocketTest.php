<?php
namespace Itseasy\Websocket\Test;

use Itseasy\Websocket;
use PHPUnit\Framework\TestCase;
use Amp\Loop;
use function Amp\delay;
use function Amp\Websocket\Client\connect;
use Amp\PHPUnit\AsyncTestCase;
use Exception;
use Amp\Websocket\ClosedException;

final class WebsocketTest extends AsyncTestCase
{
    public function testWebsocket()
    {
        $messages = [
            "Hello",
            "Ping: 1",
            "Ping: 2",
            "Ping: 3",
            "Goodbye"
        ];

        $result = [];

        $connection = yield connect('ws://127.0.0.1:13370/ws/echo');
        yield $connection->send($messages[0]);

        while ($message = yield $connection->receive()) {
            $payload = yield $message->buffer();
            printf("Received: %s\n", $payload);

            $result[] = $payload;

            if ($payload === "Goodbye") {
                $connection->close();
                break;
            }

            yield delay(1000); // Pause the coroutine for 1 second.

            for($i = 1; $i < count($messages); $i++) {
                yield $connection->send($messages[$i]);
            }
        }

        $this->setTimeout(30);

        $this->assertEquals(count($result), count($messages));
    }
}
