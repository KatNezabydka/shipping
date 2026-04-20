<?php

declare(strict_types=1);

namespace Shipping\HttpClient;

use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;
use UnexpectedValueException;

readonly abstract class BaseHttpClient
{
    public function __construct(
        protected HttpClientInterface $client,
        protected LoggerInterface $logger,
        protected SerializerInterface&NormalizerInterface $serializer,
    ) {
    }

    /**
     * @param array<string, mixed> $query
     *
     * @throws Throwable
     */
    protected function get(string $url, array $query = []): string
    {
        try {
            $response = $this->client->request('GET', $url, [
                'query' => $query,
            ]);

            return $response->getContent();

        } catch (Throwable $error) {
            $this->logger->error('HTTP GET failed', [
                'url' => $url,
                'exception' => $error,
            ]);

            throw $error;
        }
    }

    /**
     * @param array<string, mixed> $body
     *
     * @throws Throwable
     */
    protected function post(string $url, array $body = []): string
    {
        try {
            $response = $this->client->request('POST', $url, [
                'json' => $body,
            ]);

            return $response->getContent();

        } catch (Throwable $error) {
            $this->logger->error('HTTP POST failed', [
                'url' => $url,
                'exception' => $error,
            ]);

            throw $error;
        }
    }

    /**
     * @return array<string, mixed>
     * @throws ExceptionInterface
     */
    protected function normalizeRequest(object $request): array
    {
        $data = $this->serializer->normalize($request);

        if (!is_array($data)) {
            throw new UnexpectedValueException('Expected normalized request to be an array.');
        }

        return $data;
    }
}