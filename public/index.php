<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require __DIR__ . '/../vendor/autoload.php';

$loader = new FilesystemLoader('templates');
$view = new Environment($loader);

$app = AppFactory::create();
$app->addErrorMiddleware(true, false, false);

$app->get('/', function (Request $request, Response $response, array $args) use ($view) {
    $body = $view->render('index.twig');
    $response->getBody()->write($body);

    return $response;
});

$app->run();