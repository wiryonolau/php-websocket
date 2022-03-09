<?php
declare(strict_types=1);

namespace Itseasy\Websocket\Middleware;

use Amp\Http\Server\Middleware as MiddlewareInterface;
use Amp\Http\Server\Request;
use Amp\Http\Server\RequestHandler;
use Amp\Promise;
use Exception;
use Laminas\Log\LoggerAwareInterface;
use Laminas\Log\LoggerAwareTrait;
use Amp\Http\Server\Response;
use Amp\Http\Status;

use function Amp\call;

class WebsocketGuardMiddleware implements MiddlewareInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function handleRequest(Request $request, RequestHandler $requestHandler): Promise
    {
        return call(function() use ($request, $requestHandler) {
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
                $this->getLogger()->debug("not allowed");

                return new Response(
                    Status::FORBIDDEN
                );
            }

            return $response;
        });
    }
}
