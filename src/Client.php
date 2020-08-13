<?php

namespace Poplary\Consul;

use Poplary\Consul\Exception\ClientException;
use Poplary\Consul\Exception\ServerException;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface as HttpClientInterface;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Psr7\Response;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class Client implements ClientInterface
{
    /** @var ClientInterface */
    private $client;
    /** @var LoggerInterface */
    private $logger;

    public function __construct(array $options = [], LoggerInterface $logger = null, HttpClientInterface $client = null)
    {
        $baseUri = 'http://127.0.0.1:8500';

        if (isset($options['base_uri'])) {
            $baseUri = $options['base_uri'];
        } elseif (false !== getenv('CONSUL_HTTP_ADDR')) {
            $baseUri = getenv('CONSUL_HTTP_ADDR');
        }

        $options = array_replace([
            'base_uri' => $baseUri,
            'http_errors' => false,
        ], $options);

        $this->client = $client ?: new GuzzleClient($options);
        $this->logger = $logger ?: new NullLogger();
    }

    public function get($url = null, array $options = [])
    {
        return $this->doRequest('GET', $url, $options);
    }

    public function head($url, array $options = [])
    {
        return $this->doRequest('HEAD', $url, $options);
    }

    public function delete($url, array $options = [])
    {
        return $this->doRequest('DELETE', $url, $options);
    }

    public function put($url, array $options = [])
    {
        return $this->doRequest('PUT', $url, $options);
    }

    public function patch($url, array $options = [])
    {
        return $this->doRequest('PATCH', $url, $options);
    }

    public function post($url, array $options = [])
    {
        return $this->doRequest('POST', $url, $options);
    }

    public function options($url, array $options = [])
    {
        return $this->doRequest('OPTIONS', $url, $options);
    }

    private function doRequest($method, $url, $options)
    {
        if (isset($options['body']) && is_array($options['body'])) {
            $options['body'] = json_encode($options['body']);
        }

        $this->logger->info(sprintf('%s "%s"', $method, $url));
        $this->logger->debug(sprintf('Requesting %s %s', $method, $url), ['options' => $options]);

        try {
            $response = $this->client->request($method, $url, $options);
        } catch (TransferException $e) {
            $message = sprintf('Something went wrong when calling consul (%s).', $e->getMessage());

            $this->logger->error($message);

            throw new ServerException($message);
        }

        $this->logger->debug(sprintf("Response:\n%s", $this->formatResponse($response)));

        if (400 <= $response->getStatusCode()) {
            $message = sprintf('Something went wrong when calling consul (%s - %s).', $response->getStatusCode(), $response->getReasonPhrase());

            $this->logger->error($message);

            $message .= "\n".(string) $response->getBody();
            if (500 <= $response->getStatusCode()) {
                throw new ServerException($message, $response->getStatusCode());
            }

            throw new ClientException($message, $response->getStatusCode());
        }

        return new ConsulResponse($response->getHeaders(), (string) $response->getBody(), $response->getStatusCode());
    }

    private function formatResponse(Response $response)
    {
        $headers = [];

        foreach ($response->getHeaders() as $key => $values) {
            foreach ($values as $value) {
                $headers[] = sprintf('%s: %s', $key, $value);
            }
        }

        return sprintf("%s\n\n%s", implode("\n", $headers), $response->getBody());
    }
}
