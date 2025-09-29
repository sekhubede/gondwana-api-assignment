<?php
namespace Tests\Services;

use PHPUnit\Framework\TestCase;
use App\Services\PayloadTransformer;

/**
 * @covers \App\Services\PayloadTransformer
 */
class PayloadTransformerMissingFieldsTest extends TestCase
{
    public function testThrowsExceptionForMissingArrival(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Missing required field: Arrival");

        (new PayloadTransformer())->transform([
            "Departure" => "05/10/2025",
            "Ages"      => [25, 12]
        ]);
    }

    public function testThrowsExceptionForMissingDeparture(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Missing required field: Departure");

        (new PayloadTransformer())->transform([
            "Arrival" => "01/10/2025",
            "Ages"    => [25, 12]
        ]);
    }

    public function testThrowsExceptionForMissingAges(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Missing required field: Ages");

        (new PayloadTransformer())->transform([
            "Arrival"   => "01/10/2025",
            "Departure" => "05/10/2025"
        ]);
    }
}