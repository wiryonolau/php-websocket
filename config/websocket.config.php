<?php

namespace Itseasy\Websocket;

return [
    "websocket" => [
        "listen_address" => [
            "0.0.0.0:13370"
        ],
        "allowed_origins" => [
            "http://localhost:8080",
        ],
        "guard" => [
            "query" => "jwt",
            "jwt" => [
                "leeway" => 0,
                "private_key" => "",
                "public_key" => "",
                "headers" => [
                    "typ" => "JWT",
                    "alg" => "RS256"
                ],
                "payloads" => [
                    "iss" => "",
                    "aud" => "",
                    "iat" => "",
                    "nbf" => "",
                    "exp" => ""
                ]
            ]
        ],
        "handlers" => [
        ],
        "middlewares" => [
        ]
    ]
];
