<?php

namespace AutoScaler;

class Counter
{
    public int $count = 0;
    public int $thresholdCount;

    public function __construct(int $thresholdCount)
    {
        $this->thresholdCount = $thresholdCount;
    }

    public function up(): void
    {
        $this->count++;
    }

    public function down(): void
    {
        $this->count--;
    }

    public function scaleUpCountBreached(): bool
    {
        return $this->count >= $this->thresholdCount;
    }

    public function scaleDownCountBreached(): bool
    {
        return $this->count <= -$this->thresholdCount;
    }

    public function reset()
    {
        $this->count = 0;
    }

}