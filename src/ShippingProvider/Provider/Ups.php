<?php

declare(strict_types=1);

namespace Shipping\ShippingProvider\Provider;

use Psr\Log\LoggerInterface;
use Shipping\DTO\Request\UpsRegisterShippingRequest;
use Shipping\Entity\Order;
use Shipping\Enum\ShippingProviderKeyEnum;
use Shipping\HttpClient\UpsHttpClientInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Throwable;

class Ups implements ShippingProviderInterface
{
    public function __construct(
        protected UpsHttpClientInterface $httpClient,
        protected LoggerInterface $logger,
        protected SerializerInterface $serializer,
    ) {
    }

    public function supports(ShippingProviderKeyEnum $shippingProviderEnum): bool
    {
        return ShippingProviderKeyEnum::UPS === $shippingProviderEnum;
    }

    public function registerShipment(Order $order): bool
    {
        $request = UpsRegisterShippingRequest::fromOrder($order);

        try {
            $this->httpClient->registerShipping($request);
        } catch (Throwable $error) {
            $this->logger->error($error->getMessage());

            return false;
        }

        return true;
    }
}
