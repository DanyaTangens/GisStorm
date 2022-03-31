<?php

namespace App\Operations;

use App\Elements\Bounds;
use App\Repository\CouplingRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetCouplings
{
    use InjectJsonInResponseTrait;

    private CouplingRepository $repository;

    public function __construct(CouplingRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @throws \JsonException
     */
    public function __invoke(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();

        $data = [
            'error' => null,
            'result' => null
        ];

        $geoJson = [
            'type' => 'FeatureCollection',
            'features' => []
        ];

        $bounds = new Bounds(
            (float)$params['sw_lat'],
            (float)$params['sw_lng'],
            (float)$params['ne_lat'],
            (float)$params['ne_lng']
        );

        try {
            $rows = $this->repository->getByBounds($bounds);

            foreach ($rows as $row) {
                $marker = [
                    'type' => 'Feature',
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [
                            $row->getLng(),
                            $row->getLat()
                        ]
                    ],
                    'properties' => [
                        'id' => $row->getId(),
                        'name' => $row->getName(),
                        'obj' => 2,
                        'type_coupling' => $row->getTypeCoupling(),
                        'description' => $row->getDescription()
                    ]
                ];

                $geoJson['features'][] = $marker;
                $data['result'] = $geoJson;
            }
        } catch (\Throwable $e) {
            $data['error'] = $e->getMessage();
        }

        return $this->injectJson($response, $data);
    }
}