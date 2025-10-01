<?php
namespace App\Services;

use DateTime;
use InvalidArgumentException;

class PayloadTransformer
{
    public function transform(array $data): array
    {
        if (!isset($data['Arrival'])) {
            throw new InvalidArgumentException("Missing required field: Arrival");
        }
        if (!isset($data['Departure'])) {
            throw new InvalidArgumentException("Missing required field: Departure");
        }
        if (!isset($data['Ages'])) {
            throw new InvalidArgumentException("Missing required field: Ages");
        }
        if (!is_array($data['Ages'])) {
            throw new InvalidArgumentException("Ages must be an array");
        }

        $arrival   = DateTime::createFromFormat('d/m/Y', $data['Arrival']);
        $departure = DateTime::createFromFormat('d/m/Y', $data['Departure']);

        if (!$arrival) {
            throw new InvalidArgumentException("Invalid date format for Arrival");
        }
        if (!$departure) {
            throw new InvalidArgumentException("Invalid date format for Departure");
        }

        $today = new DateTime('today');
        if ($arrival < $today) {
            throw new InvalidArgumentException("Arrival date cannot be in the past");
        }
        if ($departure <= $arrival) {
            throw new InvalidArgumentException("Departure date must be after arrival date");
        }

        $guests = [];
        foreach ($data['Ages'] as $age) {
            if (!is_numeric($age) || $age < 0) {
                throw new InvalidArgumentException("Invalid age value");
            }
            $guests[] = [
                'Age Group' => $age >= 18 ? 'Adult' : 'Child',
                'Age'       => (int) $age
            ];
        }

        return [
            'Unit Type ID' => $data['Unit Type ID'] ?? -2147483637,
            'Arrival'      => $arrival->format('Y-m-d'),
            'Departure'    => $departure->format('Y-m-d'),
            'Guests'       => $guests
        ];
    }
}