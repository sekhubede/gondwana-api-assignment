<?php
namespace Tests\Services;

use PHPUnit\Framework\TestCase;
use App\Services\PayloadTransformer;

/**
 * @covers \App\Services\PayloadTransformer
 */
class PayloadTransformerHappyPathTest extends TestCase
{
    private PayloadTransformer $transformer;

    protected function setUp(): void
    {
        $this->transformer = new PayloadTransformer();
    }

    public function testTransformsPayloadCorrectly(): void
    {
        $input = [
            "Unit Name" => "Desert Lodge",
            "Arrival"   => "01/10/2025",
            "Departure" => "05/10/2025",
            "Occupants" => 2,
            "Ages"      => [30, 12, 18, 17]
        ];

        $result = $this->transformer->transform($input);

        $this->assertSame("2025-10-01", $result["Arrival"]);
        $this->assertSame("2025-10-05", $result["Departure"]);

        // Adults
        $this->assertSame("Adult", $result["Guests"][0]["Age Group"]); // 30
        $this->assertSame("Adult", $result["Guests"][2]["Age Group"]); // 18

        // Children
        $this->assertSame("Child", $result["Guests"][1]["Age Group"]); // 12
        $this->assertSame("Child", $result["Guests"][3]["Age Group"]); // 17
    }

    public function testHandlesDefaultUnitTypeId(): void
    {
        $result = $this->transformer->transform([
            "Arrival"   => "01/10/2025",
            "Departure" => "05/10/2025",
            "Ages"      => [25]
        ]);

        $this->assertSame(-2147483637, $result["Unit Type ID"]);
    }
}