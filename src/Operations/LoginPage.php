<?php

namespace App\Operations;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Twig\Environment;

class LoginPage
{
    private Environment $view;

    public function __construct(Environment $view)
    {
        $this->view = $view;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $body = $this->view->render('login.twig');
        $response->getBody()->write($body);

        return $response;
    }
}