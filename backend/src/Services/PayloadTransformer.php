<?php
namespace App\Services;

use InvalidArgumentException;
use DateTime;

class PayloadTransformer
{
    /**
     * Transform incoming frontend payload into Gondwana API schema.
     * 
     * @param array $data
     * @return array
     * @throws InvalidArgumentException if input is invalid
     */
    public function transform(array $data): array
    {
        $this->validateRequiredFields($data);
        $arrivalFormatted   = $this->parseDate($data['Arrival'], 'Arrival');
        $departureFormatted = $this->parseDate($data['Departure'], 'Departure');

        if ($arrivalFormatted > $departureFormatted) {
            throw new InvalidArgumentException("Arrival date must be before departure date");
        }

        $guests = $this->transformGuests($data['Ages']);

        return [
            'Unit Type ID' => $data['Unit Type ID'] ?? -2147483637,
            'Arrival'      => $arrivalFormatted,
            'Departure'    => $departureFormatted,
            'Guests'       => $guests
        ];
    }

    private function validateRequiredFields(array $data): void
    {
        foreach (['Arrival', 'Departure', 'Ages'] as $field) {
            if (empty($data[$field])) {
                throw new InvalidArgumentException("Missing required field: {$field}");
            }
        }

        if (!is_array($data['Ages'])) {
            throw new InvalidArgumentException("Ages must be an array");
        }
    }

    private function parseDate(string $date, string $field): string
    {
        $parsed = DateTime::createFromFormat('d/m/Y', $date);
        if (!$parsed) {
            throw new InvalidArgumentException("Invalid date format for {$field}, expected dd/mm/yyyy");
        }
        return $parsed->format('Y-m-d');
    }

    private function transformGuests(array $ages): array
    {
        $guests = [];
        foreach ($ages as $age) {
            if (!is_int($age) || $age < 0) {
                throw new InvalidArgumentException("Invalid age value: {$age}");
            }

            $guests[] = [
                'Age Group' => $age >= 18 ? 'Adult' : 'Child'
            ];
        }
        return $guests;
    }
}