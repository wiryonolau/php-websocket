<?php

namespace Itseasy\Websocket;

class Config
{
    protected $listenAddresses = ["0.0.0.0:13370"];
    protected $allowedOrigins = ["http://localhost:8080"];
    protected $guard;

    protected $handlers = [];
    protected $middlewares = [];

    public function __construct(array $config = [])
    {
        if (!empty($config["listen_address"])) {
            $this->setListenAddresses($config["listen_address"]);
        }

        if (!empty($config["allowed_origins"])) {
            $this->setAllowedOrigins($config["allowed_origins"]);
        }

        if (!empty($config["guard"])) {
            $this->guard = new GuardConfig($config["guard"]);
        } else {
            $this->guard = new GuardConfig();
        }

        if (!empty($config["middlewares"])) {
            $this->setMiddlewares($config["middlewares"]);
        }

        if (!empty($config["handlers"])) {
            $this->setHandlers($config["handlers"]);
        }
    }

    public function getGuard() : GuardConfig
    {
        return $this->guard;
    }

    public function getListenAddresses() : array
    {
        return $this->listenAddresses;
    }

    public function setListenAddresses(array $addresses = []) : void
    {
        $this->listenAddresses = $addresses;
    }

    public function getAllowedOrigins() : array
    {
        return $this->allowedOrigins;
    }

    public function setAllowedOrigins(array $allowed_origins = []) : void
    {
        $this->allowedOrigins = $allowed_origins;
    }

    public function getMiddlewares() : array
    {
        return $this->middlewares;
    }

    public function setMiddlewares(array $middlewares = []) : void
    {
        $this->middlewares = $middlewares;
    }

    public function getHandlers() : array
    {
        return $this->handlers;
    }

    public function setHandlers(array $handlers = []): void
    {
        $this->handlers = $handlers;
    }
}
