<?php

namespace App\Infrastructure;

use App\Boundary\Controller\ContactController;
use App\Boundary\Controller\TestController;
use App\Domain\ContactRepository;
use App\Infrastructure\Repository\DatabaseContactRepository;
use DI\ContainerBuilder;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Psr\Container\ContainerInterface;
use function DI\autowire;

class ContainerFactory
{
    public static function create(): ContainerInterface
    {
        $builder = new ContainerBuilder();

        $builder->addDefinitions([
            Connection::class => function () {
                // Keeping these values simple for the sake of the test.
                $connectionParameters = [
                    'driver'   => 'pdo_mysql',
                    'host'     => 'mysql',
                    'dbname'   => 'mailing-list',
                    'user'     => 'test',
                    'password' => 'test',
                ];

                return DriverManager::getConnection($connectionParameters);
            },

            RouteMapper::class => autowire(),

            // Controllers.
            ContactController::class => autowire(),
            TestController::class => autowire(),

            //Repositories
            ContactRepository::class => \DI\autowire(DatabaseContactRepository::class),
        ]);

        return $builder->build();
    }
}