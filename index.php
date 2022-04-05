<?php

use App\Handler\DefaultErrorHandler;
use App\Middleware\AuthMiddleware;
use App\Middleware\LoginValidateMiddleware;
use App\Operations\AddCoupling;
use App\Operations\DeleteCoupling;
use App\Operations\EditCoupling;
use App\Operations\EditMoveCoupling;
use App\Operations\GetCoupling;
use App\Operations\LoginPage;
use App\Operations\MapPage;
use App\Operations\UserLogin;
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

session_cache_limiter(false);
session_start();

$container = $builder->build();

AppFactory::setContainer($container);

$app = AppFactory::create();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorMiddleware->setDefaultErrorHandler(DefaultErrorHandler::class);

$app->addBodyParsingMiddleware();

$app->get('/', MapPage::class);
$app->get('/test', MapPage::class)->add(AuthMiddleware::class);

$app->get('/login', LoginPage::class);
$app->post('/login', UserLogin::class)->add(LoginValidateMiddleware::class);
$app->get('/logout', function ($request, $response, $args) {
    unset($_SESSION['user']);
    session_regenerate_id();

    return $response
        ->withHeader('Location', 'login')
        ->withStatus(302);
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