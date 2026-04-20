<?php

declare(strict_types=1);

namespace Shipping\HttpClient;

use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Throwable;

readonly abstract class BaseHttpClient
{
    public function __construct(
        protected HttpClientInterface $client,
        protected LoggerInterface $logger,
        protected SerializerInterface&NormalizerInterface $serializer,
    ) {
    }

    /**
     * @throws Throwable
     * @throws ExceptionInterface
     */
    protected function get(string $url, array $query = []): string
    {
        try {
            $response = $this->client->request('GET', $url, [
                'query' => $query,
            ]);

            return $response->getContent();

        } catch (ExceptionInterface|Throwable $error) {
            $this->logger->error('HTTP GET failed', [
                'url' => $url,
                'exception' => $error,
            ]);

            throw $error;
        }
    }

    /**
     * @throws Throwable
     * @throws ExceptionInterface
     */
    protected function post(string $url, array $body = []): string
    {
        try {
            $response = $this->client->request('POST', $url, [
                'json' => $body,
            ]);

            return $response->getContent();

        } catch (ExceptionInterface|Throwable $error) {
            $this->logger->error('HTTP POST failed', [
                'url' => $url,
                'exception' => $error,
            ]);

            throw $error;
        }
    }
}