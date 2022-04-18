<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Twig\Environment;

class LoginValidateMiddleware
{
    private ResponseFactoryInterface $responseFactory;
    private Environment $view;

    public function __construct(Environment $view, ResponseFactoryInterface $responseFactory)
    {
        $this->view = $view;
        $this->responseFactory = $responseFactory;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $error = [];
        $params = $request->getParsedBody();
        if(trim($params['login']) === '') {
            $error[] = 'Введите логин';
        }
        if($params['password'] === '') {
            $error[] = 'Введите пароль';
        }
        if (!empty($error)) {
            $body = $this->view->render('login.twig', [
                'errors' => $error
            ]);
            $response = $this->responseFactory->createResponse();
            $response->getBody()->write($body);

            return $response;
        }

        return $handler->handle($request);
    }
}