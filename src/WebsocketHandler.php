<?php

namespace Itseasy\Websocket;

use Amp\Http\Server\Middleware;
use Amp\Websocket\Server\ClientHandler;
use Laminas\Log\LoggerAwareInterface;
use Laminas\Log\LoggerInterface;
use Amp\Websocket\Server\Websocket as AmpWebsocket;

class WebsocketHandler
{
    protected $route;
    protected $method;
    protected $handler;
    protected $middlewares = [];

    public function __construct(
        string $route,
        string $method,
        ClientHandler $handler,
        array $middlewares = []
    ) {
        $this->route = $route;

        $this->method  = is_null($method) ? "GET" : $method;

        $this->setHandler($handler);

        foreach ($middlewares as $middleware) {
            $this->addMiddleware($middleware);
        }
    }

    public function setLogger(LoggerInterface $logger) : void
    {
        if ($this->handler instanceof LoggerAwareInterface) {
            $this->handler->setLogger($logger);
        }

        foreach ($this->middlewares as $index => $middleware) {
            if ($middleware instanceof LoggerAwareInterface) {
                $this->middlewares[$index]->setLogger($logger);
            }
        }
    }

    public function getRoute() : string
    {
        return $this->route;
    }

    public function getMethod() : string
    {
        return $this->method;
    }

    public function getHandler() : AmpWebsocket
    {
        return $this->handler;
    }

    public function setHandler(ClientHandler $handler) : void
    {
        $this->handler = new AmpWebsocket($handler);
    }

    public function getMiddlewares() : array
    {
        return $this->middlewares;
    }

    public function addMiddleware(Middleware $middleware) : void
    {
        $this->middlewares[] = $middleware;
    }
}
