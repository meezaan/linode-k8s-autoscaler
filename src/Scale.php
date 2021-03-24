<?php


namespace AutoScaler;


class Scale
{
    public float $thresholdUpPercentage;
    public float $thresholdDownPercentage;
    public float $usedPercentage;
    public float $requestedPercentage;
    public float $thresholdRequestPercentage;

    public function __construct(float $thresholdUpPercentage, float $thresholdDownPercentage, float $usedPercentage, float $requestedPercentage, float $thresholdRequestPercentage)
    {
        $this->thresholdUpPercentage = $thresholdUpPercentage;
        $this->thresholdDownPercentage = $thresholdDownPercentage;
        $this->usedPercentage = $usedPercentage;
        $this->requestedPercentage = $requestedPercentage;
        $this->thresholdRequestPercentage = $thresholdRequestPercentage;
    }

    public function scaleUp()
    {
        return $this->usedPercentage > $this->thresholdUpPercentage
            || $this->requestedPercentage > $this->thresholdRequestPercentage;

    }

    public function scaleDown()
    {
        return $this->usedPercentage < $this->thresholdDownPercentage
            || $this->requestedPercentage < $this->thresholdRequestPercentage;
    }
}