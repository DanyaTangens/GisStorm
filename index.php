<?php

use App\Operations\AddCoupling;
use App\Operations\DeleteCoupling;
use App\Operations\EditCoupling;
use App\Operations\GetCoupling;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use App\Operations\GetCouplings;

require __DIR__ . '/vendor/autoload.php';

$loader = new FilesystemLoader('public/templates');
$view = new Environment($loader);

$config = include 'config/database.php';
$dsn = $config['dsn'];
$username = $config['username'];
$password = $config['password'];
try {
    $connection = new PDO($dsn, $username, $password);
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $exception) {
//    echo 'Database error: ' . $exception->getMessage();
//    die();
}

$app = AppFactory::create();

$app->addErrorMiddleware(true, false, false);
$app->addBodyParsingMiddleware();

//ROUTES
$app->get('/', function (Request $request, Response $response, array $args) use ($view) {
    $body = $view->render('index.twig');
    $response->getBody()->write($body);

    return $response;
});
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