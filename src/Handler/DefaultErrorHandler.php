<?php

namespace App\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Throwable;

class DefaultErrorHandler
{
    public function __invoke(
        ServerRequestInterface $request,
        Throwable $exception,
        bool $displayErrorDetails,
        bool $logErrors,
        bool $logErrorDetails,
        ?LoggerInterface $logger = null
    ): ResponseInterface
    {
//        $payload = ['error' => $exception->getMessage()];
//
//        $response = $app->getResponseFactory()->createResponse();
//        $response->getBody()->write(
//            json_encode($payload, JSON_UNESCAPED_UNICODE)
//        );
//
//        return $response;
    }
}