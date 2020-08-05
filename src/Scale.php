<?php


namespace AutoScaler;


class Scale
{
    public float $thresholdUpPercentage;
    public float $thresholdDownPercentage;
    public float $usedPercentage;

    public function __construct(float $thresholdUpPercentage, float $thresholdDownPercentage, float $usedPercentage)
    {
        $this->thresholdUpPercentage = $thresholdUpPercentage;
        $this->thresholdDownPercentage = $thresholdDownPercentage;
        $this->usedPercentage = $usedPercentage;
    }

    public function scaleUp()
    {
        return $this->usedPercentage > $this->thresholdUpPercentage;

    }

    public function scaleDown()
    {
        return $this->usedPercentage < $this->thresholdDownPercentage;
    }
}