<?php

namespace App\Operations;

use App\Repository\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UserLogin
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $_SESSION['user'] = 'danya';

        return $response
            ->withHeader('Location', '/')
            ->withStatus(302);
    }
}