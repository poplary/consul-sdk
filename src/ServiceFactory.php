<?php

namespace Poplary\Consul;

use Poplary\Consul\Services\Agent;
use Poplary\Consul\Services\AgentInterface;
use Poplary\Consul\Services\Catalog;
use Poplary\Consul\Services\CatalogInterface;
use Poplary\Consul\Services\Health;
use Poplary\Consul\Services\HealthInterface;
use Poplary\Consul\Services\KV;
use Poplary\Consul\Services\KVInterface;
use Poplary\Consul\Services\Session;
use Poplary\Consul\Services\SessionInterface;
use GuzzleHttp\Client as GuzzleClient;
use Psr\Log\LoggerInterface;

final class ServiceFactory
{
    private static $services = [
        AgentInterface::class => Agent::class,
        CatalogInterface::class => Catalog::class,
        HealthInterface::class => Health::class,
        SessionInterface::class => Session::class,
        KVInterface::class => KV::class,

        // for backward compatibility:
        AgentInterface::SERVICE_NAME => Agent::class,
        CatalogInterface::SERVICE_NAME => Catalog::class,
        HealthInterface::SERVICE_NAME => Health::class,
        SessionInterface::SERVICE_NAME => Session::class,
        KVInterface::SERVICE_NAME => KV::class,
    ];

    private $client;

    public function __construct(array $options = [], LoggerInterface $logger = null, GuzzleClient $guzzleClient = null)
    {
        $this->client = new Client($options, $logger, $guzzleClient);
    }

    public function get($service)
    {
        if (!array_key_exists($service, self::$services)) {
            throw new \InvalidArgumentException(sprintf('The service "%s" is not available. Pick one among "%s".', $service, implode('", "', array_keys(self::$services))));
        }

        $class = self::$services[$service];

        return new $class($this->client);
    }
}
