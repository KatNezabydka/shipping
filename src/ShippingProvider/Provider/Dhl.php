<?php

declare(strict_types=1);

namespace Shipping\ShippingProvider\Provider;

use Psr\Log\LoggerInterface;
use Shipping\DTO\Request\DhlRegisterShippingRequest;
use Shipping\Entity\Order;
use Shipping\Enum\ShippingProviderKeyEnum;
use Shipping\HttpClient\DhlHttpClientInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Throwable;

class Dhl implements ShippingProviderInterface
{
    public function __construct(
        protected DhlHttpClientInterface $httpClient,
        protected LoggerInterface $logger,
        protected SerializerInterface $serializer,
    ) {
    }

    public function supports(ShippingProviderKeyEnum $shippingProviderEnum): bool
    {
        return ShippingProviderKeyEnum::DHL === $shippingProviderEnum;
    }

    public function registerShipment(Order $order): bool
    {
        $request = DhlRegisterShippingRequest::fromOrder($order);

        try {
            return $this->httpClient->registerShipping($request);
        } catch (Throwable $error) {
            $this->logger->error($error->getMessage());

            return false;
        }
    }
}
