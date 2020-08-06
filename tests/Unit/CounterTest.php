<?php

namespace AutoScaler\tests\Unit;

use AutoScaler\Counter;
use PHPUnit\Framework\TestCase;

class CounterTest extends TestCase
{
    /**
     * @covers \AutoScaler\Counter
     */
    public function testCounter()
    {
        $counter = new Counter(3);
        $counter->up();
        $this->assertFalse($counter->scaleUpCountBreached());
        $this->assertFalse($counter->scaleDownCountBreached());
        $counter->up();
        $counter->up();
        $this->assertTrue($counter->scaleUpCountBreached());
        $this->assertEquals(3, $counter->count);
        $this->assertFalse($counter->scaleDownCountBreached());
        $counter->reset();
        $counter->down();
        $counter->down();
        $counter->down();
        $this->assertFalse($counter->scaleUpCountBreached());
        $this->assertTrue($counter->scaleDownCountBreached());
        $this->assertEquals(-3, $counter->count);
    }

}