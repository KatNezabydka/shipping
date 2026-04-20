<?php

declare(strict_types=1);

namespace Shipping\HttpClient;

use Psr\Log\LoggerInterface;
use Shipping\DTO\Request\UpsRegisterShippingRequest;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

readonly class UpsHttpClient extends BaseHttpClient implements UpsHttpClientInterface
{
    private const string SHIPPING_REGISTER_URL = '/register';

    public function __construct(
        HttpClientInterface $upsClient,
        LoggerInterface $logger,
        SerializerInterface&NormalizerInterface $serializer,
    ) {
        parent::__construct($upsClient, $logger, $serializer);
    }

    /**
     * @throws Throwable
     */
    public function registerShipping(UpsRegisterShippingRequest $request): bool
    {
        $this->post(
            self::SHIPPING_REGISTER_URL,
            $this->normalizeRequest($request)
        );

        return true;
    }
}
