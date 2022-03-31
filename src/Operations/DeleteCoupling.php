<?php

namespace App\Operations;

use App\Repository\CouplingRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class DeleteCoupling
{
    use InjectJsonInResponseTrait;

    private CouplingRepository $repository;

    public function __construct(CouplingRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
       $this->repository->deleteById($args['id']);

       return $this->injectJson($response, []);
    }
}