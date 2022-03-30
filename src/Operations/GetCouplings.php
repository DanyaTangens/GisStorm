<?php

namespace App\Operations;

use App\Elements\Bounds;
use App\Repository\CouplingRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetCouplings
{
    private CouplingRepository $repository;
    private array $params;

    public function __construct(CouplingRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $this->params = $request->getQueryParams();
        return $this->prepareGeoJson($response);
    }

    private function prepareGeoJson(Response $response): Response
    {
        $data = [
            'error' => null,
            'result' => null
        ];

        $geoJson = [
            'type' => 'FeatureCollection',
            'features' => []
        ];

        $bounds = new Bounds(
            (float)$this->params['sw_lat'],
            (float)$this->params['sw_lng'],
            (float)$this->params['ne_lat'],
            (float)$this->params['ne_lng']
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

        $response->getBody()->write(json_encode($data));

        return $response;
    }
}