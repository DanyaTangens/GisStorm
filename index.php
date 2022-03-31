<?php

use App\Operations\AddCoupling;
use App\Operations\DeleteCoupling;
use App\Operations\EditCoupling;
use App\Operations\GetCoupling;
use App\Operations\MapPage;
use DevCoder\DotEnv;
use DI\ContainerBuilder;
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

// Add Error Middleware
$app->addErrorMiddleware(true, false, false);

$app->addBodyParsingMiddleware();

$app->get('/', MapPage::class);
$app->group('/api', function (RouteCollectorProxy $app) {
    $app->group('/v1', function (RouteCollectorProxy $group) {
        $group->group('/couplings', function (RouteCollectorProxy $api) {
            $api->get('', GetCouplings::class);
            $api->get('/{id}', GetCoupling::class);
            $api->post('', AddCoupling::class);
            $api->put('', EditCoupling::class);
            $api->delete('/{id}', DeleteCoupling::class);
        });
    });
});

$app->run();