<?php

namespace App\Operations;

use App\Elements\Coupling;
use App\Repository\CouplingRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AddCoupling
{
    use InjectJsonInResponseTrait;

    private CouplingRepository $repository;

    public function __construct(CouplingRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @throws \JsonException
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(Request $request, Response $response): Response
    {
        $body = $request->getParsedBody();
        $coupling  = new Coupling(
            0,
            $body['name'],
            $body['type_coupling'],
            $body['description'],
            $body['lat'],
            $body['lng'],
        );
        $id = $this->repository->addCoupling($coupling);

        $data = [
            'result' => $id
        ];

        return $this->injectJson($response, $data);
    }


}