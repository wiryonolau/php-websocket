<?php
declare(strict_types=1);

namespace Itseasy\Websocket;

use Amp\Http\Server\HttpServer;
use Amp\Http\Server\Middleware;
use Amp\Http\Server\Middleware\Internal\MiddlewareRequestHandler;
use Amp\Http\Server\RequestHandler;
use Amp\Http\Server\Router;
use Amp\Loop;
use Amp\Promise;
use Amp\Socket\Server as AmpSocketServer;
use Amp\Websocket\Server\ClientHandler;
use Amp\Websocket\Server\Websocket as AmpWebsocket;
use Laminas\Log\LoggerAwareInterface;
use Laminas\Log\LoggerAwareTrait;
use Laminas\Log\PsrLoggerAdapter;

class Server implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected $config = [];
    protected $middlewares = [];

    public function __construct(array $config = [], array $middlewares = [])
    {
        $this->config = $config;
        $this->middlewares = $middlewares;
    }

    public function start() : void
    {
        foreach ($this->config["listen_address"] as $address) {
            $server[] = AmpSocketServer::listen($address);
        }

        $router = new Router();

        foreach($this->config["handlers"] as $handler) {
            if (!$handler["handler"] instanceof ClientHandler) {
                continue;
            }

            call_user_func_array(
                [$router, "addRoute"],
                array_merge(
                    [
                        $handler["method"],
                        $handler["route"],
                        new AmpWebsocket($handler["handler"])
                    ],
                    $handler["middlewares"]
                )
            );
        }

        // Stack global middlewares to router
        $router = $this->stack($router, $this->middlewares);

        Loop::run(function() use ($server, $router) : Promise {
            $httpServer = new HttpServer(
                $server,
                $router,
                new PsrLoggerAdapter($this->getLogger())
            );

            return $httpServer->start();
        });
    }

    /***
     * Cannot use call_user_func_array to call Amp\Http\Server\Middleware\stack;
     * Duplicate stack function here
     ***/
    protected function stack(RequestHandler $requestHandler, array $middlewares = []): RequestHandler
    {
        foreach (array_reverse($middlewares) as $middleware) {
            if (!$middleware instanceof Middleware) {
                continue;
            }

            $requestHandler = new MiddlewareRequestHandler($middleware, $requestHandler);
        }

        return $requestHandler;
    }
}
