<?php

namespace AutoScaler\tests\Unit;

use AutoScaler\Scale;
use PHPUnit\Framework\TestCase;

class ScaleTest extends TestCase
{
    /**
     * @covers \AutoScaler\Scale
     */
    public function testScale()
    {
        $scale = new Scale(65, 30, 50);
        $this->assertFalse($scale->scaleDown());
        $this->assertFalse($scale->scaleUp());

        $scale = new Scale(65, 30, 67);
        $this->assertFalse($scale->scaleDown());
        $this->assertTrue($scale->scaleUp());

        $scale = new Scale(65, 30, 29);
        $this->assertTrue($scale->scaleDown());
        $this->assertFalse($scale->scaleUp());

    }

}