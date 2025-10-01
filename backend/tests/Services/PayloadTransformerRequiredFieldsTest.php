<?php
namespace Tests\Services;

use PHPUnit\Framework\TestCase;
use App\Services\PayloadTransformer;

/**
 * @covers \App\Services\PayloadTransformer
 */
class PayloadTransformerRequiredFieldsTest extends TestCase
{
    private PayloadTransformer $transformer;

    protected function setUp(): void
    {
        $this->transformer = new PayloadTransformer();
    }

    public function testThrowsExceptionForMissingArrival(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Missing required field: Arrival");

        $this->transformer->transform([
            "Departure" => "05/10/2025",
            "Ages"      => [25, 12]
        ]);
    }

    public function testThrowsExceptionForMissingDeparture(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Missing required field: Departure");

        $this->transformer->transform([
            "Arrival" => "01/10/2025",
            "Ages"    => [25, 12]
        ]);
    }

    public function testThrowsExceptionForMissingAges(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Missing required field: Ages");

        $this->transformer->transform([
            "Arrival"   => "01/10/2025",
            "Departure" => "05/10/2025"
        ]);
    }
}