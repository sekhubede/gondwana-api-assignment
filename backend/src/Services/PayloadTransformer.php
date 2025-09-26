<?php
namespace App\Services;

class PayloadTransformer
{
    public function transform(array $data): array
    {
        return [
            'unitName'  => $data['unitName'] ?? 'N/A',
            'arrival'   => $data['arrival'] ?? null,
            'departure' => $data['departure'] ?? null,
            'occupants' => $data['occupants'] ?? [],
            'ages'      => $data['ages'] ?? []
        ];
    }
}