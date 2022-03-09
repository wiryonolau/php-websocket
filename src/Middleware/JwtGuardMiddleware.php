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
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use DateTime;
use DateTimeZone;
use Itseasy\Websocket\Config;

use function Amp\call;

class JwtGuardMiddleware implements MiddlewareInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function handleRequest(Request $request, RequestHandler $requestHandler): Promise
    {
        return call(function () use ($request, $requestHandler) {
            try {
                $query_paramaters = [];
                parse_str($request->getUri()->getQuery(), $query_paramaters);
                $jwt = $query_paramaters[$this->config->getGuard()->getQuery()];
                list($header, $payload, $signature) = explode(".", $jwt);

                $header = json_decode(base64_decode($header), true);
                $payload = json_decode(base64_decode($payload), true);

                // Make sure minimum header key exist
                if (!empty(array_diff(
                    $this->config->getGuard()->getRequiredHeaders(),
                    array_keys($header)
                ))) {
                    throw new Exception("Invalid Header");
                }

                // Make sure minimum payload key exist
                if (!empty(array_diff(
                    $this->config->getGuard()->getRequiredPayloads(),
                    array_keys($payload)
                ))) {
                    throw new Exception("Missing Payload attribute");
                }

                // Decode already check iat, nbf and exp

                // Allow 10 second leeway
                JWT::$leeway = $this->config->getGuard()->getLeeway();

                // Set current time
                JWT::$timestamp = time();

                $actual_payload = JWT::decode(
                    $jwt,
                    new Key($this->config->getGuard()->getPublicKey(), $header["alg"])
                );

                // Check issuance
                if ($actual_payload->iss !== $this->config->getGuard()->getPayload("iss")) {
                    throw new Exception("Invalid issuer");
                }

                // Check audience, audience can be array
                if (!is_array($this->config->getGuard()->getPayload("aud"))) {
                    $auds = [$this->config->getGuard()->getPayload("aud")];
                } else {
                    $auds = $this->config->getGuard()->getPayload("aud");
                }

                if (!empty($auds) and !in_array($actual_payload->aud, $auds)) {
                    throw new Exception("Invalid audience");
                }

                // Set jwt attribute on request to be use by handler
                $request->setAttribute("jwt", $actual_payload);

                return yield $requestHandler->handleRequest($request);
            } catch (Exception $e) {
                $this->getLogger()->debug($e->getMessage());
                return new Response(
                    Status::FORBIDDEN
                );
            }
        });
    }
}
