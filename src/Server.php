<?php
declare(strict_types=1);

namespace Itseasy\Websocket;

use Itseasy\Websocket\Config;
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

    protected $router;
    protected $config = [];
    protected $handlers = [];
    protected $middlewares = [];

    public function __construct(
        Config $config,
        array $handlers = [],
        array $middlewares = []
    ) {
        $this->config = $config;
        $this->handlers = $handlers;
        $this->middlewares = $middlewares;

        $this->router = new Router();
    }

    public function prepare()
    {
        foreach ($this->config->getListenAddresses() as $address) {
            $server[] = AmpSocketServer::listen($address);
        }

        foreach ($this->handlers as $handler) {
            call_user_func_array(
                [$this->router, "addRoute"],
                array_merge(
                    [
                        $handler->getMethod(),
                        $handler->getRoute(),
                        $handler->getHandler()
                    ],
                    $handler->getMiddlewares()
                )
            );
        }

        // Stack global middlewares to router
        $this->router = $this->stack($this->router, $this->middlewares);

        return new HttpServer(
            $server,
            $this->router,
            new PsrLoggerAdapter($this->getLogger())
        );
    }

    public function start()
    {
        Loop::run(function () use ($server) : Promise {
            $httpServer = $this->prepare();
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
