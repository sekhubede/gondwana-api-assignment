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
    public function format(array $apiResponse): array
    {
        return [
            'totalCharge'       => $apiResponse['Total Charge'] ?? 0,
            'roomsAvailable'    => $apiResponse['Rooms'] ?? 0,
            'rates'             => array_map(function ($leg) {
                return [
                    'description'   => $leg['Special Rates Description'] ?? 'N/A',
                    'price'         => $leg['Effective Average Daily Rate'] ?? 0,
                    'category'      => $leg['Category'] ?? 'Unknown'
                ];
            }, $apiResponse['Legs'] ?? [])
        ];
    }
}