<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\PopulateDB;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$db = new PopulateDB();
$db->populate();

if ($_ENV['APP_ENV'] !== 'production') {
    header("Access-Control-Allow-Origin: *");
    header(
        "Access-Control-Allow-Headers: " .
        "X-Requested-With, Content-Type, Origin, " .
        "Cache-Control, Pragma, Authorization, Accept, Accept-Encoding"
    );
}

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    $r->post('/graphql', [App\Controller\GraphQL::class, 'handle']);
});

$routeInfo = $dispatcher->dispatch(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI']
);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        echo $handler($vars);
        break;
}