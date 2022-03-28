<?php

namespace App\Operations;

use App\Repository\CouplingRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetCoupling
{
    private CouplingRepository $repository;

    public function __construct(CouplingRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        return $this->transform($response);
    }

    private function transform(Response $response): Response
    {
        $data = $this->repository->getById(2);

        $response->getBody()->write(json_encode($data));

        return $response;
    }
}