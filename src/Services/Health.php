<?php

namespace Poplary\Consul\Services;

use Poplary\Consul\Client;
use Poplary\Consul\OptionsResolver;

final class Health implements HealthInterface
{
    private $client;

    public function __construct(Client $client = null)
    {
        $this->client = $client ?: new Client();
    }

    public function node($node, array $options = [])
    {
        $params = [
            'query' => OptionsResolver::resolve($options, ['dc']),
        ];

        return $this->client->get('/v1/health/node/'.$node, $params);
    }

    public function checks($service, array $options = [])
    {
        $params = [
            'query' => OptionsResolver::resolve($options, ['dc']),
        ];

        return $this->client->get('/v1/health/checks/'.$service, $params);
    }

    public function service($service, array $options = [])
    {
        $params = [
            'query' => OptionsResolver::resolve($options, ['dc', 'tag', 'passing']),
        ];

        return $this->client->get('/v1/health/service/'.$service, $params);
    }

    public function state($state, array $options = [])
    {
        $params = [
            'query' => OptionsResolver::resolve($options, ['dc']),
        ];

        return $this->client->get('/v1/health/state/'.$state, $params);
    }
}
