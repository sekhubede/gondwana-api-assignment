<?php
namespace Tests\Services;

use PHPUnit\Framework\TestCase;
use App\Services\PayloadTransformer;

/**
 * @covers \App\Services\PayloadTransformer
 */
class PayloadTransformerValidationTest extends TestCase
{
    public function testThrowsExceptionForInvalidAgesType(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Ages must be an array");

        (new PayloadTransformer())->transform([
            "Arrival"   => "01/10/2025",
            "Departure" => "05/10/2025",
            "Ages"      => "not an array"
        ]);
    }

    public function testThrowsExceptionForInvalidDateFormat(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid date format for Arrival");

        (new PayloadTransformer())->transform([
            "Arrival"   => "2025-10-01", // wrong format
            "Departure" => "05/10/2025",
            "Ages"      => [30]
        ]);
    }

    public function testThrowsExceptionForArrivalAfterDeparture(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Arrival date must be before departure date");

        (new PayloadTransformer())->transform([
            "Arrival"   => "05/10/2025",
            "Departure" => "01/10/2025",
            "Ages"      => [30]
        ]);
    }

    public function testThrowsExceptionForInvalidAge(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid age value");

        (new PayloadTransformer())->transform([
            "Arrival"   => "01/10/2025",
            "Departure" => "05/10/2025",
            "Ages"      => [30, -5]
        ]);
    }
}