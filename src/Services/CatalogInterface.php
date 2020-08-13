<?php

namespace Poplary\Consul\Services;

interface CatalogInterface
{
    const SERVICE_NAME = 'catalog';

    public function register($node);

    public function deregister($node);

    public function datacenters();

    public function nodes(array $options = []);

    public function node($node, array $options = []);

    public function services(array $options = []);

    public function service($service, array $options = []);
}
