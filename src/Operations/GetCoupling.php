<?php

namespace App\Operations;

use App\Repository\CouplingRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetCoupling
{
    use InjectJsonInResponseTrait;

    private CouplingRepository $repository;

    public function __construct(CouplingRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     * @throws \JsonException
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $coupling = $this->repository->getById($args['id']);
        $data = [
            'id' => $coupling->getId(),
            'name' => $coupling->getName(),
            'obj' => 2,
            'type_coupling' => $coupling->getTypeCoupling(),
            'description' => $coupling->getDescription(),
            'lat' => $coupling->getLat(),
            'lng' => $coupling->getLng(),
        ];

        return $this->injectJson($response, $data);
    }
}