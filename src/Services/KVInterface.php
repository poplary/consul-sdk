<?php

namespace Poplary\Consul\Services;

interface KVInterface
{
    const SERVICE_NAME = 'kv';

    public function get($key, array $options = []);

    public function put($key, $value, array $options = []);

    public function delete($key, array $options = []);
}
