<?php


namespace AutoScaler;


class Scale
{
    public float $thresholdUpPercentage;
    public float $thresholdDownPercentage;
    public float $usedPercentage;
    public float $requestedPercentage;
    public float $thresholdRequestUpPercentage;
    public float $thresholdRequestDownPercentage;

    public function __construct(float $thresholdUpPercentage, float $thresholdDownPercentage, float $usedPercentage, float $requestedPercentage, float $thresholdRequestUpPercentage, float $thresholdRequestDownPercentage)
    {
        $this->thresholdUpPercentage = $thresholdUpPercentage;
        $this->thresholdDownPercentage = $thresholdDownPercentage;
        $this->usedPercentage = $usedPercentage;
        $this->requestedPercentage = $requestedPercentage;
        $this->thresholdRequestUpPercentage = $thresholdRequestUpPercentage;
        $this->thresholdRequestDownPercentage = $thresholdRequestDownPercentage;
    }

    public function scaleUp()
    {
        return $this->usedPercentage > $this->thresholdUpPercentage
            || $this->requestedPercentage > $this->thresholdRequestUpPercentage;

    }

    public function scaleDown()
    {
        return $this->usedPercentage < $this->thresholdDownPercentage
            || $this->requestedPercentage < $this->thresholdRequestDownPercentage;
    }
}