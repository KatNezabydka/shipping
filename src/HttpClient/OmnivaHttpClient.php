<?php

declare(strict_types=1);

namespace Shipping\HttpClient;

use Psr\Log\LoggerInterface;
use Shipping\DTO\Request\OmnivaFindPickupPointRequest;
use Shipping\DTO\Request\OmnivaRegisterShippingRequest;
use Shipping\DTO\Response\OmnivaFindPickupPointResponse;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

readonly class OmnivaHttpClient extends BaseHttpClient implements OmnivaHttpClientInterface
{
    private const string FIND_PICK_UP_URL = '/pickup/find';
    private const string SHIPPING_REGISTER_URL = '/register';

    public function __construct(
        HttpClientInterface $omnivaClient,
        LoggerInterface $logger,
        SerializerInterface&NormalizerInterface $serializer,
    ) {
        parent::__construct($omnivaClient, $logger, $serializer);
    }

    /**
     * @throws Throwable
     */
    public function findPickup(OmnivaFindPickupPointRequest $request): OmnivaFindPickupPointResponse
    {
        $json = $this->get(
            self::FIND_PICK_UP_URL,
            $this->serializer->normalize($request)
        );

        return $this->serializer->deserialize(
            $json,
            OmnivaFindPickupPointResponse::class,
            'json'
        );
    }

    /**
     * @throws Throwable
     */
    public function registerShipping(OmnivaRegisterShippingRequest $request): bool
    {
        $this->post(
            self::SHIPPING_REGISTER_URL,
            $this->serializer->normalize($request)
        );

        return true;
    }
}