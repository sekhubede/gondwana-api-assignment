<?php
namespace App\Services;

class ResponseFormatter
{
    public const FIELD_LOCATION_ID     = 'Location ID';
    public const FIELD_TOTAL_CHARGE    = 'Total Charge';
    public const FIELD_EXTRAS_CHARGE   = 'Extras Charge';
    public const FIELD_BOOKING_GROUP   = 'Booking Group ID';
    public const FIELD_LEGS            = 'Legs';
    public const FIELD_GUESTS          = 'Guests';

    /**
     * Extracts a clean response from Gondwana API raw payload.
     *
     * @param array $response
     * @return array
     */
    public function format(array $response): array
    {
        return [
            self::FIELD_LOCATION_ID   => $response[self::FIELD_LOCATION_ID] ?? null,
            self::FIELD_TOTAL_CHARGE  => $response[self::FIELD_TOTAL_CHARGE] ?? null,
            self::FIELD_EXTRAS_CHARGE => $response[self::FIELD_EXTRAS_CHARGE] ?? null,
            self::FIELD_BOOKING_GROUP => $response[self::FIELD_BOOKING_GROUP] ?? null,
            self::FIELD_LEGS          => array_map(function ($leg) {
                return [
                    self::FIELD_TOTAL_CHARGE => $leg[self::FIELD_TOTAL_CHARGE] ?? null,
                    self::FIELD_GUESTS       => $leg[self::FIELD_GUESTS] ?? []
                ];
            }, $response[self::FIELD_LEGS] ?? [])
        ];
    }
}