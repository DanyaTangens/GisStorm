<?php

use App\Operations\AddCoupling;
use App\Operations\DeleteCoupling;
use App\Operations\EditCoupling;
use App\Operations\GetCoupling;
use App\Operations\MapPage;
use DevCoder\DotEnv;
use DI\ContainerBuilder;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use App\Operations\GetCouplings;

require __DIR__ . '/vendor/autoload.php';

$builder = new ContainerBuilder();
$builder->addDefinitions('config/Container.php');
(new DotEnv(__DIR__ . '\.env'))->load();

$container = $builder->build();

AppFactory::setContainer($container);

$app = AppFactory::create();
$app->addErrorMiddleware(true, false, false);
$app->addBodyParsingMiddleware();

$app->get('/', MapPage::class);
$app->group('/api', function (RouteCollectorProxy $app) {
    $app->group('/v1', function (RouteCollectorProxy $group) {
        $group->group('/couplings', function (RouteCollectorProxy $api) {
            $api->get('', GetCouplings::class);
            $api->get('/:id', GetCoupling::class);
            $api->post('', AddCoupling::class);
            $api->put('', EditCoupling::class);
            $api->delete('/:id', DeleteCoupling::class);
        });
    });
});

$app->post('/test', function (Request $request, Response $response, array $args) {
    $data = $request->getParsedBody();
    $body = $response->getBody();
    $body->write(json_encode($data, JSON_THROW_ON_ERROR));

    return $response
        ->withHeader('content-type', 'application/json')
        ->withBody($body);
});

$app->get('/test', function (Request $request, Response $response, array $args) {
    $data = ['name' => 'maks'];
    $body = $response->getBody();
    $body->write(json_encode($data, JSON_THROW_ON_ERROR));

    return $response
        ->withHeader('content-type', 'application/json')
        ->withBody($body);
});

$app->run();