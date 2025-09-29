<?php
namespace App\Services;

class ResponseFormatter
{
    /**
     * Extraccts a clean response from Gondwana API raw payload.
     * 
     * @param array $apiResponse
     * @return array
     */
    public function format(array $response): array
    {
        return [
            'Location ID'      => $response['Location ID'] ?? null,
            'Total Charge'     => $response['Total Charge'] ?? null,
            'Extras Charge'    => $response['Extras Charge'] ?? null,
            'Booking Group ID' => $response['Booking Group ID'] ?? null,
            'Legs' => array_map(function ($leg) {
                return [
                    'Total Charge' => $leg['Total Charge'] ?? null,
                    'Guests'       => $leg['Guests'] ?? []
                ];
            }, $response['Legs'] ?? [])
        ];
    }
}