<?php

declare(strict_types=1);

namespace App\HttpClient;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Throwable;

readonly abstract class BaseHttpClient
{
    public function __construct(
        protected ClientInterface $client,
        protected LoggerInterface $logger,
        protected SerializerInterface $serializer,
    ) {
    }

    /**
     * @throws Throwable
     * @throws GuzzleException
     */
    protected function get(string $url, array $query = []): string
    {
        try {
            $response = $this->client->request('GET', $url, [
                'query' => $query,
            ]);

            return $response->getBody()->getContents();

        } catch (Throwable $error) {
            $this->logger->error('HTTP GET failed', [
                'url' => $url,
                'exception' => $error,
            ]);

            throw $error;
        }
    }

    /**
     * @throws GuzzleException
     * @throws Throwable
     */
    protected function post(string $url, array $body = []): string
    {
        try {
            $response = $this->client->request('POST', $url, [
                'json' => $body,
            ]);

            return $response->getBody()->getContents();

        } catch (Throwable $error) {
            $this->logger->error('HTTP POST failed', [
                'url' => $url,
                'exception' => $error,
            ]);

            throw $error;
        }
    }
}