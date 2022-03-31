<?php

namespace App\Operations;

use App\Elements\Coupling;
use App\Repository\CouplingRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class EditCoupling
{
    use InjectJsonInResponseTrait;

    private CouplingRepository $repository;

    public function __construct(CouplingRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $body = $request->getParsedBody();
        $coupling  = new Coupling(
            $body['id'],
            $body['name'],
            $body['type_coupling'],
            $body['description'],
            $body['lat'],
            $body['lng'],
        );
        $this->repository->editCoupling($coupling);

        $data = [
            'result' => 'Пашел нахуй?'
        ];

        return $this->injectJson($response, $data);
    }
}