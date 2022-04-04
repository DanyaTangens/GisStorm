<?php

use App\Handler\DefaultErrorHandler;
use App\Middleware\AuthMiddleware;
use App\Operations\AddCoupling;
use App\Operations\DeleteCoupling;
use App\Operations\EditCoupling;
use App\Operations\EditMoveCoupling;
use App\Operations\GetCoupling;
use App\Operations\MapPage;
use DevCoder\DotEnv;
use DI\ContainerBuilder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use App\Operations\GetCouplings;

require __DIR__ . '/vendor/autoload.php';

$builder = new ContainerBuilder();
$builder->addDefinitions('config/Container.php');
(new DotEnv(__DIR__ . '/.env'))->load();

//phpinfo();
$container = $builder->build();

AppFactory::setContainer($container);

$app = AppFactory::create();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorMiddleware->setDefaultErrorHandler(DefaultErrorHandler::class);

$app->addBodyParsingMiddleware();

$app->get('/', MapPage::class);
$app->get('/test', MapPage::class)->add(AuthMiddleware::class);

$app->get('/login', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
    $response->getBody()->write("Hello world!");
    return $response;
});

$app->group('/api', function (RouteCollectorProxy $app) {
    $app->group('/v1', function (RouteCollectorProxy $group) {
        $group->group('/couplings', function (RouteCollectorProxy $api) {
            $api->get('', GetCouplings::class);
            $api->get('/{id}', GetCoupling::class);
            $api->post('', AddCoupling::class);
            $api->put('', EditCoupling::class);
            $api->put('/{id}/move', EditMoveCoupling::class);
            $api->delete('/{id}', DeleteCoupling::class);
        });
    });
});

$app->run();