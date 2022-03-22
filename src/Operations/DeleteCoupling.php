<?php

namespace App\Operations;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class DeleteCoupling
{
    public function __invoke(Request $request, Response $response): Response
    {
        return $this->transform($response);
    }

    private function transform(Response $response): Response
    {
        $userList = [
            'username' => 'Maks'
        ];
        $response->getBody()->write(json_encode($userList));

        return $response;
    }
}