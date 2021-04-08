<?php

namespace AutoScaler\tests\Unit;

use AutoScaler\Helper\Resource;
use PHPUnit\Framework\TestCase;

class ResourceTest extends TestCase
{
    /**
     * @covers \AutoScaler\Counter
     */
    public function testCounter()
    {
        $this->assertEquals(100, Resource::getCpuMilliValue(100));
    }

}