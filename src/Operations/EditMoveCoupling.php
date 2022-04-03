<?php

namespace App\Operations;

use App\Elements\Coupling;
use App\Repository\CouplingRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class EditMoveCoupling
{
    use InjectJsonInResponseTrait;

    private CouplingRepository $repository;

    public function __construct(CouplingRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $body = $request->getParsedBody();
        $coupling  = new Coupling(
            $args['id'],
            null,
            null,
           null,
            $body['lat'],
            $body['lng'],
        );
        $this->repository->editCouplingMove($coupling);

        $coupling = $this->repository->getById($args['id']);
        $data = [
            'result' => [
                'id' => $coupling->getId(),
                'name' => $coupling->getName(),
                'obj' => 2,
                'type_coupling' => $coupling->getTypeCoupling(),
                'description' => $coupling->getDescription(),
                'lat' => $coupling->getLat(),
                'lng' => $coupling->getLng(),
            ]
        ];

        return $this->injectJson($response, $data);
    }
}