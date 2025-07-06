<?php

declare(strict_types=1);

use FastRoute\RouteCollector;
use App\Infrastructure\ContainerFactory;
use App\Infrastructure\RouteMapper;
use function FastRoute\simpleDispatcher;

require_once __DIR__ . '/../vendor/autoload.php';

$container = ContainerFactory::create();

$routeMapper = $container->get(RouteMapper::class);

// Using the route mapper does feel a bit overkill for this use case, but would allow for simpler expansion of the API.
$dispatcher = simpleDispatcher(function(RouteCollector $r) use ($routeMapper) {
    $routeMapper->mapRoutes($r);
});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo json_encode(['error' => 'Not Found']);
        break;

    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
        break;

    case FastRoute\Dispatcher::FOUND:
        [$handler, $vars] = [$routeInfo[1], $routeInfo[2]];
        [$class, $method] = $handler;

        $controller = $container->get($class);

        $response = call_user_func_array([$controller, $method], $vars);

        header('Content-Type: application/json');
        echo json_encode($response);
        break;
}

