<?php

namespace App\Operations;

use App\Repository\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Twig\Environment;

class UserLogin
{
    private UserRepository $userRepository;
    private Environment $view;

    public function __construct(UserRepository $userRepository, Environment $view)
    {
        $this->userRepository = $userRepository;
        $this->view = $view;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $params = $request->getParsedBody();
        if ($this->userRepository->isPasswordValid($params['login'], $params['password'])) {
            $user = $this->userRepository->getUserByLogin($params['login']);
            $_SESSION['user'] = $user;

            return $response
                ->withHeader('Location', '/')
                ->withStatus(302);
        }
        $body = $this->view->render('login.twig', [
            'errors' => [
                'Неверный логин или пароль'
            ]
        ]);

        $response->getBody()->write($body);

        return $response;
    }
}