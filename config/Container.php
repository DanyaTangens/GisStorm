<?php

declare(strict_types=1);

use App\Middleware\AuthMiddleware;
use App\Operations\UserLogin;
use App\Twig\AssetExtension;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use function DI\autowire;
use function DI\get;

return [
    ResponseFactoryInterface::class => function () {
        return new ResponseFactory();
    },
    'server.params' => $_SERVER,
    FilesystemLoader::class => autowire()
        ->constructorParameter('paths', 'public/templates'),

    Environment::class => autowire()
        ->constructorParameter('loader', get(FilesystemLoader::class))
        ->method('addExtension', get(AssetExtension::class)),

    Connection::class => function () {
        $params = [
            'driver' => 'pdo_mysql',
            'dbname' => getenv('DB_NAME'),
            'user' => getenv('DB_USER'),
            'password' => getenv('DB_PASSWORD'),
            'host' => getenv('DB_HOST'),
            'port' => getenv('DB_PORT'),
        ];

        return DriverManager::getConnection($params);
    },

    AssetExtension::class => autowire()
        ->constructorParameter('serverParams', get('server.params')),
];