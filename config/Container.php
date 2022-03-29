<?php

declare(strict_types=1);

use App\Database;
use App\Twig\AssetExtension;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use function DI\autowire;
use function DI\get;

return [
    'server.params' => $_SERVER,
    FilesystemLoader::class => autowire()
        ->constructorParameter('paths', 'public/templates'),

    Environment::class => autowire()
        ->constructorParameter('loader', get(FilesystemLoader::class))
        ->method('addExtension', get(AssetExtension::class)),

    Database::class => autowire()
        ->constructorParameter('connection', get(PDO::class)),

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

    PDO::class => autowire()
        ->constructor(
            getenv('DATABASE_DSN'),
            getenv('DATABASE_USERNAME'),
            getenv('DATABASE_PASSWORD'),
            []
        )
        ->method('setAttribute', PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION)
        ->method('setAttribute', PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC),

    AssetExtension::class => autowire()
        ->constructorParameter('serverParams', get('server.params')),
];