<?php
namespace Tests\Services;

use PHPUnit\Framework\TestCase;
use App\Services\PayloadTransformer;

/**
 * @covers \App\Services\PayloadTransformer
 */
class PayloadTransformerInvalidValuesTest extends TestCase
{
    private PayloadTransformer $transformer;

    protected function setUp(): void
    {
        $this->transformer = new PayloadTransformer();
    }

    public function testThrowsExceptionForInvalidAgesType(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Ages must be an array");

        $this->transformer->transform([
            "Arrival"   => "01/10/2025",
            "Departure" => "05/10/2025",
            "Ages"      => "not an array"
        ]);
    }

    public function testThrowsExceptionForInvalidAge(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid age value");

        $this->transformer->transform([
            "Arrival"   => "01/10/2025",
            "Departure" => "05/10/2025",
            "Ages"      => [30, -5]
        ]);
    }

    /**
     * @dataProvider invalidDateProvider
     */
    public function testInvalidDateFormats(string $arrival, string $departure, string $expectedMessage): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);

        $this->transformer->transform([
            "Arrival"   => $arrival,
            "Departure" => $departure,
            "Ages"      => [25]
        ]);
    }

    public static function invalidDateProvider(): array
    {
        return [
            ["2025-10-01", "05/10/2025", "Invalid date format for Arrival"],   // wrong format for Arrival
            ["01/10/2025", "2025-10-05", "Invalid date format for Departure"], // wrong format for Departure
        ];
    }

    public function testThrowsExceptionForArrivalInThePast(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Arrival date cannot be in the past");

        $yesterday = (new \DateTimeImmutable('-1 day'))->format('d/m/Y');
        $tomorrow  = (new \DateTimeImmutable('+1 day'))->format('d/m/Y');

        $this->transformer->transform([
            "Arrival"   => $yesterday,
            "Departure" => $tomorrow,
            "Ages"      => [25]
        ]);
    }

    public function testThrowsExceptionForArrivalAfterDeparture(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Departure date must be after arrival date");

        $this->transformer->transform([
            "Arrival"   => "05/10/2025",
            "Departure" => "01/10/2025",
            "Ages"      => [30]
        ]);
    }
}