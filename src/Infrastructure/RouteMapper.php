<?php

namespace App\Infrastructure;

use App\Boundary\Controller\ContactController;
use App\Boundary\Controller\TestController;
use FastRoute\RouteCollector;

class RouteMapper
{
    public function mapRoutes(RouteCollector $routeCollector)
    {
        $routeCollector->addRoute('GET', '/test', [TestController::class, 'test']);
        $routeCollector->addGroup('/contacts', function (RouteCollector $r) {
            $r->addRoute('POST', '', [ContactController::class, 'create']);
            $r->addRoute('GET', '', [ContactController::class, 'list']);
            $r->addRoute('DELETE', '/{id}', [ContactController::class, 'delete']);
        });
    }
}