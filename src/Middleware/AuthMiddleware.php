<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class AuthMiddleware
{
    private ResponseFactoryInterface $responseFactory;

    public function __construct(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $response = $this->responseFactory->createResponse();
        if (!isset($_SESSION['user'])) {
            return $response
                ->withHeader('Location', 'login')
                ->withStatus(302);
        }

        return $handler->handle($request);
    }
}