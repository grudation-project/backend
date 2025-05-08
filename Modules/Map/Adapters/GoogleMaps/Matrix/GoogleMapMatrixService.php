<?php

namespace Modules\Map\Adapters\GoogleMaps\Matrix;

use Illuminate\Support\Facades\Http;
use Modules\Map\Contracts\MatrixContract;
use Modules\Map\Exceptions\MapException;
use Symfony\Component\HttpFoundation\Response;

class GoogleMapMatrixService implements MatrixContract
{
    /**
     * @throws MapException
     */
    public function getCoordinates(array $coordinates, array $additionalData = []): array
    {
        $origins = $this->prepareLocations($coordinates);

        $apiKey = config('services.map.google_maps.api_key');
        $destinations = $origins;

        $rawResponse = Http::get('https://maps.googleapis.com/maps/api/distancematrix/json', [
            'units' => 'metric',
            'origins' => $origins,
            'destinations' => $destinations,
            'key' => $apiKey,
        ]);

        $response = $rawResponse->json();
        //        $response = [
        //            "destination_addresses" => [
        //                "270 شارع الملك فيصل، أولى الهرم، قسم العمرانية، الجيزة،، Oula Al Haram, El Talbia, Giza Governorate 3531343, Egypt",
        //                "283 King Faisal St, Oula Al Haram, El Talbia, Giza Governorate 3537206, Egypt",
        //                "2528+CV8, Oula Al Haram, El Talbia, Giza Governorate 3537206, Egypt"
        //            ],
        //            "origin_addresses" => [
        //                "270 شارع الملك فيصل، أولى الهرم، قسم العمرانية، الجيزة،، Oula Al Haram, El Talbia, Giza Governorate 3531343, Egypt",
        //                "283 King Faisal St, Oula Al Haram, El Talbia, Giza Governorate 3537206, Egypt",
        //                "2528+CV8, Oula Al Haram, El Talbia, Giza Governorate 3537206, Egypt"
        //            ],
        //            "rows" => [
        //                [
        //                    "elements" => [
        //                        [
        //                            "distance" => [
        //                                "text" => "1 m",
        //                                "value" => 0
        //                            ],
        //                            "duration" => [
        //                                "text" => "1 min",
        //                                "value" => 0
        //                            ],
        //                            "status" => "OK"
        //                        ],
        //                        [
        //                            "distance" => [
        //                                "text" => "0.5 km",
        //                                "value" => 452
        //                            ],
        //                            "duration" => [
        //                                "text" => "2 mins",
        //                                "value" => 128
        //                            ],
        //                            "status" => "OK"
        //                        ],
        //                        [
        //                            "distance" => [
        //                                "text" => "0.2 km",
        //                                "value" => 211
        //                            ],
        //                            "duration" => [
        //                                "text" => "2 mins",
        //                                "value" => 96
        //                            ],
        //                            "status" => "OK"
        //                        ]
        //                    ]
        //                ],
        //                [
        //                    "elements" => [
        //                        [
        //                            "distance" => [
        //                                "text" => "1.3 km",
        //                                "value" => 1287
        //                            ],
        //                            "duration" => [
        //                                "text" => "4 mins",
        //                                "value" => 239
        //                            ],
        //                            "status" => "OK"
        //                        ],
        //                        [
        //                            "distance" => [
        //                                "text" => "1 m",
        //                                "value" => 0
        //                            ],
        //                            "duration" => [
        //                                "text" => "1 min",
        //                                "value" => 0
        //                            ],
        //                            "status" => "OK"
        //                        ],
        //                        [
        //                            "distance" => [
        //                                "text" => "1.2 km",
        //                                "value" => 1230
        //                            ],
        //                            "duration" => [
        //                                "text" => "5 mins",
        //                                "value" => 297
        //                            ],
        //                            "status" => "OK"
        //                        ]
        //                    ]
        //                ],
        //                [
        //                    "elements" => [
        //                        [
        //                            "distance" => [
        //                                "text" => "1.4 km",
        //                                "value" => 1374
        //                            ],
        //                            "duration" => [
        //                                "text" => "4 mins",
        //                                "value" => 256
        //                            ],
        //                            "status" => "OK"
        //                        ],
        //                        [
        //                            "distance" => [
        //                                "text" => "87 m",
        //                                "value" => 87
        //                            ],
        //                            "duration" => [
        //                                "text" => "1 min",
        //                                "value" => 17
        //                            ],
        //                            "status" => "OK"
        //                        ],
        //                        [
        //                            "distance" => [
        //                                "text" => "1 m",
        //                                "value" => 0
        //                            ],
        //                            "duration" => [
        //                                "text" => "1 min",
        //                                "value" => 0
        //                            ],
        //                            "status" => "OK"
        //                        ]
        //                    ]
        //                ]
        //            ],
        //            "status" => "OK"
        //        ];

        if ($response['status'] == 'OK') {

            $result = [];

            foreach ($response['rows'] as $row) {
                $tmpRow = [];

                foreach ($row['elements'] as $e) {
                    $tmpRow[] = $e['distance']['value'];
                }
                $result[] = $tmpRow;
            }

            return $result;
        }

        throw new MapException($response['error_message'], Response::HTTP_BAD_REQUEST);
    }

    private function prepareLocations(array $locations): string
    {
        return implode('|', array_map(fn ($loc) => "$loc[1],$loc[0]", $locations));
    }
}
