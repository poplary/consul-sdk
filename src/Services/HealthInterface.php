<?php

namespace Poplary\Consul\Services;

interface HealthInterface
{
    const SERVICE_NAME = 'health';

    public function node($node, array $options = []);

    public function checks($service, array $options = []);

    public function service($service, array $options = []);

    public function state($state, array $options = []);
}
