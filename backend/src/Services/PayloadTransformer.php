<?php
namespace App\Services;

class PayloadTransformer
{
    public function transform(array $data): array
    {
        $arrival = \DateTime::createFromFormat('d/m/Y', $data['Arrival'] ?? '');
        $departure = \DateTime::createFromFormat('d/m/Y', $data['Departure'] ?? '');

        $arrivalFormatted = $arrival ? $arrival->format('Y-m-d') : null;
        $departureFormatted = $departure ? $departure->format('Y-m-d') : null;

        $guests = [];
        if (!empty($data['Ages']) && is_array($data['Ages'])) {
            foreach ($data['Ages'] as $age) {
                $guests[] = [
                    'Age Group' => $age >= 18 ? 'Adult' : 'Child'
                ];
            }
        }

        return [
            'Unit Type ID'  => -2147483637, // Hardcoded for testing
            'Arrival'       => $arrivalFormatted,
            'Departure'     => $departureFormatted,
            'Guests'        => $guests
        ];
    }
}