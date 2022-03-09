<?php

namespace Itseasy\Websocket;

use Exception;

class GuardConfig
{
    // Query param key
    protected $query = "jwt";

    // Jwt leeway
    protected $leeway = 0;

    protected $privateKey = "";
    protected $publicKey = "";

    // Minimum header key requirement and default header value
    protected $headers = [];

    // Minimum payload key requirement and default payload value
    protected $payloads = [];

    public function __construct(array $config = [])
    {
        if (!empty($config["query"])) {
            $this->setQuery($config["query"]);
        }

        if (!empty($config["jwt"]["leeway"])) {
            $this->setLeeway($config["jwt"]["leeway"]);
        }

        if (!empty($config["jwt"]["private_key"])) {
            $this->setPrivateKey($config["jwt"]["private_key"]);
        }

        if (!empty($config["jwt"]["public_key"])) {
            $this->setPublicKey($config["jwt"]["public_key"]);
        }

        if (!empty($config["jwt"]["headers"])) {
            $this->setHeaders($config["jwt"]["headers"]);
        }

        if (!empty($config["jwt"]["payloads"])) {
            $this->setPayloads($config["jwt"]["payloads"]);
        }
    }

    public function getQuery() : string
    {
        return $this->query;
    }

    public function setQuery(string $query = "jwt") : void
    {
        $this->query = $query;
    }

    public function getLeeway() : int
    {
        return $this->leeway;
    }

    public function setLeeway(int $leeway = 0) : void
    {
        $this->leeway = $leeway;
    }

    public function getPrivateKey() : string
    {
        return $this->privateKey;
    }

    public function setPrivateKey(string $private_key = "") : void
    {
        $this->privateKey = $private_key;
    }

    public function getPublicKey() : string
    {
        return $this->publicKey;
    }

    public function setPublicKey(string $public_key = "") : void
    {
        $this->publicKey = $public_key;
    }

    public function getRequiredHeaders() : array
    {
        return array_keys($this->headers);
    }

    public function getHeaders() : array
    {
        return $this->headers;
    }

    public function getHeader(string $name) : string
    {
        try {
            return $this->headers[$name];
        } catch (Exception $e) {
            return "";
        }
    }

    public function setHeaders(array $headers = []) : void
    {
        $this->headers = $headers;
    }

    public function getRequiredPayloads() : array
    {
        return array_keys($this->payloads);
    }

    public function getPayload(string $name) : string
    {
        try {
            return $this->payloads[$name];
        } catch (Exception $e) {
            return "";
        }
    }

    public function getPayloads() : array
    {
        return $this->payloads;
    }

    public function setPayloads(array $payloads = []) : void
    {
        $this->payloads = $payloads;
    }
}
