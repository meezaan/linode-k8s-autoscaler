<?php

namespace AutoScaler\Kubernetes;

use KubernetesClient\Client;
use KubernetesClient\ResourceList;

class Nodes
{
    private Client $client;

    private ResourceList $nodes;

    private ResourceList $nodeMetrics;

    private float $availableCpu;

    private float $availableMemory;

    private float $usedCpu;

    private float $usedMemory;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->nodes = $this->client->createList("/api/v1/nodes");
        $this->nodeMetrics = $this->client->createList("/apis/metrics.k8s.io/v1beta1/nodes");
        $this->calculateAvailableResources();
        $this->calculateUsedResources();
    }

    public function getNodes(): ResourceList
    {
        return $this->nodes;
    }

    private function calculateAvailableResources(): void
    {
        $this->availableCpu = 0;
        $this->availableMemory = 0;
        foreach ($this->nodes->stream() as $node) {
            $this->availableCpu += (float) $node['status']['allocatable']['cpu'];
            $this->availableMemory += (float) $node['status']['allocatable']['memory'];
        }
    }

    private function calculateUsedResources(): void
    {
        $this->usedCpu = 0;
        $this->usedMemory = 0;
        foreach ($this->nodeMetrics->stream() as $node) {
            $this->usedCpu += (intval($node['usage']['cpu']));
            $this->usedMemory += (float) $node['usage']['memory'];
        }
        $this->usedCpu = (float) $this->usedCpu / 1000000000;
    }

    public function getAvailableCpu(): float
    {
        return $this->availableCpu;
    }

    public function getAvailableMemory(): float
    {
        return $this->availableMemory;
    }

    public function getUsedCpu(): float
    {
        return $this->usedCpu;
    }

    public function getUsedMemory(): float
    {
        return $this->usedMemory;
    }

    public function getUsedCpuPercent(): float
    {
        return ($this->usedCpu / $this->availableCpu) * 100;
    }

    public function getUsedMemoryPercent(): float
    {
        return ($this->usedMemory / $this->availableMemory) * 100;
    }
}