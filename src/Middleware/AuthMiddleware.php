<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class AuthMiddleware
{
    private array $serverParams;
    private ResponseFactoryInterface $responseFactory;

    public function __construct(ResponseFactoryInterface $responseFactory, array $serverParams)
    {
        $this->responseFactory = $responseFactory;
        $this->serverParams = $serverParams;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        if ($this->serverParams['account'] !== null) {
            return $this->responseFactory->createResponse()
                ->withHeader('Location', 'login')
                ->withStatus(302);
        }

        return $handler->handle($request);
    }
}