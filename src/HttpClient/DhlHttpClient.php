<?php

declare(strict_types=1);

namespace Shipping\HttpClient;

use Psr\Log\LoggerInterface;
use Shipping\DTO\Request\DhlRegisterShippingRequest;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

readonly class DhlHttpClient extends BaseHttpClient implements DhlHttpClientInterface
{
    private const string SHIPPING_REGISTER_URL = '/register';

    public function __construct(
        HttpClientInterface $dhlClient,
        LoggerInterface $logger,
        SerializerInterface&NormalizerInterface $serializer,
    ) {
        parent::__construct($dhlClient, $logger, $serializer);
    }

    /**
     * @throws Throwable
     */
    public function registerShipping(DhlRegisterShippingRequest $request): bool
    {
        $this->post(
            self::SHIPPING_REGISTER_URL,
            $this->normalizeRequest($request)
        );

        return true;
    }
}
