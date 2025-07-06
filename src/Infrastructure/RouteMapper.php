<?php

namespace App\Infrastructure;

use App\Boundary\Controller\TestController;
use FastRoute\RouteCollector;

class RouteMapper
{
    public function mapRoutes(RouteCollector $routeCollector)
    {
        $routeCollector->addRoute('GET', '/test', [TestController::class, 'test']);
    }
}