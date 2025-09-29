<?php
use PHPUnit\Framework\TestCase;
use App\Services\ResponseFormatter;

/**
 * @covers \App\Services\ResponseFormatter
 */
class ResponseFormatterTest extends TestCase
{
    public function testFormatsResponseCorrectly()
    {
        $formatter = new ResponseFormatter();

        $apiResponse = [
            'Location ID'       => 123,
            'Total Charge'      => 5000,
            'Extras Charge'     => 200,
            'Booking Group ID'  => 'Test Group',
            'Legs'              => [
                [
                    'Total Charge'  => 3000,
                    'Guests'        => [
                        ['Age Group' => 'Adult', 'Age' => 30],
                        ['Age Group' => 'Child', 'Age' => 10]
                    ]
                ]
            ]
        ];

        $result = $formatter->format($apiResponse);

        $this->assertEquals(123, $result['Location ID']);
        $this->assertEquals(5000, $result['Total Charge']);
        $this->assertEquals(200, $result['Extras Charge']);
        $this->assertEquals('Test Group', $result['Booking Group ID']);
        $this->assertCount(1, $result['Legs']);
        $this->assertEquals('Adult', $result['Legs'][0]['Guests'][0]['Age Group']);
    }

    public function testHandlesEmptyLegs()
    {
        $formatter = new ResponseFormatter();

        $apiResponse = [
            'Location ID'       => 456,
            'Total Charge'      => 0,
            'Extras Charge'     => 0,
            'Booking Group ID'  => 'Empty Case',
            'Legs'              => []
        ];

        $result = $formatter->format($apiResponse);

        $this->assertEquals(456, $result['Location ID']);
        $this->assertEquals([], $result['Legs']);
    }
}