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
        $scale = new Scale(65, 30, 50, 60, 70, 55);
        $this->assertFalse($scale->scaleDown());
        $this->assertFalse($scale->scaleUp());

        $scale = new Scale(65, 30, 67, 60, 70, 60);
        $this->assertFalse($scale->scaleDown());
        $this->assertTrue($scale->scaleUp());

        $scale = new Scale(65, 30, 63, 70, 60, 50);
        $this->assertFalse($scale->scaleDown());
        $this->assertTrue($scale->scaleUp());

        $scale = new Scale(65, 30, 29, 70, 80, 75);
        $this->assertTrue($scale->scaleDown());
        $this->assertFalse($scale->scaleUp());

        $scale = new Scale(65, 30, 40, 70, 80, 75);
        $this->assertTrue($scale->scaleDown());
        $this->assertFalse($scale->scaleUp());

    }

}