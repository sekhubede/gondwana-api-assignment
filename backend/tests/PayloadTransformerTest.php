<?php
use PHPUnit\Framework\TestCase;
use App\Services\PayloadTransformer;

/**
 * @covers \App\Services\PayloadTransformer
 */
class PayloadTransformerTest extends TestCase
{
    public function testTransformsPayloadCorrectly(): void
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

        $this->assertSame("2025-10-01", $result["Arrival"]);
        $this->assertSame("2025-10-05", $result["Departure"]);
        $this->assertSame("Adult", $result["Guests"][0]["Age Group"]);
        $this->assertSame("Child", $result["Guests"][1]["Age Group"]);
    }

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

    public function testHandlesDefaultUnitTypeId(): void
    {
        $result = (new PayloadTransformer())->transform([
            "Arrival"   => "01/10/2025",
            "Departure" => "05/10/2025",
            "Ages"      => [30]
        ]);

        $this->assertSame(-2147483637, $result["Unit Type ID"]);
    }
}
