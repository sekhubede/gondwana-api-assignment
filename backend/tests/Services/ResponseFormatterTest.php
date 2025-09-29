<?php
namespace Tests\Services;

use PHPUnit\Framework\TestCase;
use App\Services\ResponseFormatter;

/**
 * @covers \App\Services\ResponseFormatter
 */
class ResponseFormatterTest extends TestCase
{
    public function testFormatsResponseCorrectly(): void
    {
        $formatter = new ResponseFormatter();

        $apiResponse = [
            'Location ID'      => 123,
            'Total Charge'     => 5000,
            'Extras Charge'    => 200,
            'Booking Group ID' => 'Test Group',
            'Legs'             => [
                [
                    'Total Charge' => 3000,
                    'Guests'       => [
                        ['Age Group' => 'Adult', 'Age' => 30],
                        ['Age Group' => 'Child', 'Age' => 10]
                    ]
                ]
            ]
        ];

        $result = $formatter->format($apiResponse);

        $this->assertSame(123, $result['Location ID']);
        $this->assertSame(5000, $result['Total Charge']);
        $this->assertSame(200, $result['Extras Charge']);
        $this->assertSame('Test Group', $result['Booking Group ID']);
        $this->assertCount(1, $result['Legs']);
        $this->assertSame('Adult', $result['Legs'][0]['Guests'][0]['Age Group']);
    }

    public function testHandlesEmptyLegs(): void
    {
        $formatter = new ResponseFormatter();

        $apiResponse = [
            'Location ID'      => 456,
            'Total Charge'     => 0,
            'Extras Charge'    => 0,
            'Booking Group ID' => 'Empty Case',
            'Legs'             => []
        ];

        $result = $formatter->format($apiResponse);

        $this->assertSame(456, $result['Location ID']);
        $this->assertSame([], $result['Legs']);
    }
}
