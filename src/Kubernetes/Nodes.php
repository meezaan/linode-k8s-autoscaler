<?php

namespace AutoScaler\Kubernetes;

use http\Encoding\Stream\Deflate;
use KubernetesClient\Client;
use KubernetesClient\ResourceList;
use AutoScaler\Helper\Resource;

class Nodes
{
    private Client $client;

    private ResourceList $nodes;

    private ResourceList $nodeMetrics;

    private float $availableCpu;

    private float $availableMemory;

    private float $usedCpu;

    private float $usedMemory;

    private float $requestedCpu;

    private float $requestedMemory;

    private float $limitCpu;

    private float $limitMemory;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->nodes = $this->client->createList("/api/v1/nodes");
        $this->pods = $this->client->createList("/api/v1/pods");
        $this->nodeMetrics = $this->client->createList("/apis/metrics.k8s.io/v1beta1/nodes");
        $this->calculateAvailableResources();
        $this->calculateUsedResources();
    }

    public function getNodes(): ResourceList
    {
        return $this->nodes;
    }

    private function calculateRequestedResources(): void
    {
        $this->requestedCpu = 0;
        $this->requestedMemory = 0;
        $this->limitCpu = 0;
        $this->limitMemory = 0;
        foreach ($this->pods->stream() as $pod) {
            foreach ($pod['containers'] as $container) {
                $this->limitCpu += (float) Resource::getCpuMilliValue($container['resources']['limits']['cpu']);
                $this->limitMemory += (float) Resource::getMemoryBytes($container['resources']['limits']['memory']);
                $this->requestedCpu += (float) Resource::getCpuMilliValue($container['resources']['requests']['cpu']);
                $this->requestedMemory += (float) Resource::getMemoryBytes($container['resources']['requests']['memory']);
            }
        }
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

    public function getRequestedCpu(): float
    {
        return $this->requestedCpu;
    }

    public function getRequestedMemory(): float
    {
        return $this->requestedMemory;
    }

    public function getLimitCpu(): float
    {
        return $this->limitCpu;
    }

    public function getLimitMemory(): float
    {
        return $this->limitMemory;
    }

    public function getRequestedCpuPercent(): float
    {
        return ($this->requestedCpu / $this->availableCpu) * 100;
    }

    public function getRequestedMemoryPercent(): float
    {
        return ($this->requestedMemory / $this->availableMemory) * 100;
    }
}