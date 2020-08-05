<?php

namespace AutoScaler\Linode\Lke;

use GuzzleHttp\Client;

class Pool
{
    public Client $client;
    public string $endpoint = 'https://api.linode.com/v4/lke/clusters/';
    private string $token;
    public int $clusterId;
    public int $poolId;

    public function __construct(string $personalAccessToken, int $clusterId, int $poolId)
    {
        $this->client = new Client();
        $this->token = $personalAccessToken;
        $this->clusterId = $clusterId;
        $this->poolId = $poolId;
    }

    public function getNodeCount(): int
    {
        $poolResponse = $this->client->get($this->endpoint . $this->clusterId . '/pools/' . $this->poolId,
        [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token
            ]
        ]
        );
        $pool = json_decode((string) $poolResponse->getBody());

        return $pool->count;
    }

    public function updateNodeCount(int $numberOfNodes): bool
    {
        $poolResponse = $this->client->put($this->endpoint . $this->clusterId . '/pools/' . $this->poolId,
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token
                ],
                'json' => [
                    'count' => $numberOfNodes
                ]

            ]
        );

        return $poolResponse->getStatusCode() == 200;
    }

}