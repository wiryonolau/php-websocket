<?php
declare(strict_types=1);

namespace Itseasy\Websocket\Middleware;

use Amp\Http\Server\Middleware as MiddlewareInterface;
use Amp\Http\Server\Request;
use Amp\Http\Server\RequestHandler;
use Amp\Http\Server\Response;
use Amp\Http\Status;
use Amp\Promise;
use Exception;
use Itseasy\Websocket\Config;
use Laminas\Log\LoggerAwareInterface;
use Laminas\Log\LoggerAwareTrait;

use function Amp\call;

class WebsocketGuardMiddleware implements MiddlewareInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function handleRequest(Request $request, RequestHandler $requestHandler): Promise
    {
        return call(function() use ($request, $requestHandler) {
            // No guard
            if (!count($this->config->getAllowedOrigins())) {
                return  yield $requestHandler->handleRequest($request);
            }

            if (!in_array(
                $request->getHeader('origin'),
                $this->config->getAllowedOrigins(),
                true
            )) {
                return new Response(
                    Status::FORBIDDEN
                );
            }
            return yield $requestHandler->handleRequest($request);
        });
    }
}
