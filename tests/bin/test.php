<?php
declare(strict_types=1);

chdir(dirname(__DIR__));

// Decline static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server') {
    $path = realpath(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    if (is_string($path) && __FILE__ !== $path && is_file($path)) {
        return false;
    }
    unset($path);
}

// Composer autoloading
include __DIR__ . '/../../vendor/autoload.php';

$app = new Itseasy\Websocket\Test\Application([
    "config_path" => [
        __DIR__."/../../config/*.config.php",
        __DIR__."/../config/*.config.php"
    ],
]);

$app->build();

$server = $app->getContainer()->get(Itseasy\Websocket\Server::class);
$server->start();