<?php
declare(strict_types=1);

namespace Itseasy\Websocket\Middleware;

use Amp\Http\Server\Middleware as MiddlewareInterface;
use Amp\Http\Server\Request;
use Amp\Http\Server\RequestHandler;
use Amp\Coroutine;
use Amp\Promise;
use Generator;
use RuntimeException;
use Laminas\Log\LoggerAwareInterface;
use Laminas\Log\LoggerAwareTrait;

class WebsocketGuardMiddleware implements MiddlewareInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function handleRequest(Request $request, RequestHandler $requestHandler): Promise
    {
        return new Coroutine($this->guard($request, $requestHandler));
    }

    public function guard(Request $request, RequestHandler $requestHandler): Generator
    {
        $response = yield $requestHandler->handleRequest($request);

        // No guard
        if (!count($this->config["allowed_origins"])) {
            return $response;
        }

        if (!in_array(
            $request->getHeader('origin'),
            $this->config["allowed_origins"],
            true
        )) {
            throw new RuntimeException("Forbidden Access");
        }

        return $response;
    }
}
