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
    public string $triggerType; // requested or used

    public function __construct(float $thresholdUpPercentage, float $thresholdDownPercentage, float $usedPercentage, float $requestedPercentage, float $thresholdRequestUpPercentage, float $thresholdRequestDownPercentage, string $triggerType)
    {
        $this->thresholdUpPercentage = $thresholdUpPercentage;
        $this->thresholdDownPercentage = $thresholdDownPercentage;
        $this->usedPercentage = $usedPercentage;
        $this->requestedPercentage = $requestedPercentage;
        $this->thresholdRequestUpPercentage = $thresholdRequestUpPercentage;
        $this->thresholdRequestDownPercentage = $thresholdRequestDownPercentage;
	$this->triggerType = $triggerType;
    }

    public function scaleUp()
    {
	    if ($triggerType == 'requested') {
		return $this->requestedPercentage > $this->thresholdRequestUpPercentage;
	    } 

	    return $this->usedPercentage > $this->thresholdUpPercentage

    }

    public function scaleDown()
    {
	    if ($triggerType == 'requested') {
                return $this->requestedPercentage < $this->thresholdRequestDownPercentage;
	    }

        return $this->usedPercentage < $this->thresholdDownPercentage
    }
}
