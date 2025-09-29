<?php
use PHPUnit\Framework\TestCase;
use App\Services\PayloadTransformer;

/**
 * @covers \App\Services\PayloadTransformer
 */
class PayloadTransformerTest extends TestCase
{
    public function testTransformsPayloadCorrectly()
    {
        $transformer = new PayloadTransformer();

        $input = [
            "Unit Name" => "Desert Lodge",
            "Arrival"   => "01/10/2025",
            "Departure" => "05/10/2025",
            "Occupants" => 2,
            "Ages"      => [30, 12]
        ];

        $result = $transformer->transform($input);

        $this->assertEquals("2025-10-01", $result["Arrival"]);
        $this->assertEquals("2025-10-05", $result["Departure"]);
        $this->assertEquals("Adult", $result["Guests"][0]["Age Group"]);
        $this->assertEquals("Child", $result["Guests"][1]["Age Group"]);
    }

    public function testThrowsExceptionForMissingArrival()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Missing required field: Arrival");

        $transformer = new PayloadTransformer();

        $input = [
            "Departure" => "05/10/2025",
            "Ages"      => [25, 12]
        ];

        $transformer->transform($input);
    }
}
