<?php

declare(strict_types=1);

namespace App\ShippingProvider\Provider;

use App\DTO\Request\UpsRegisterShippingRequest;
use App\Entity\Order;
use App\Enum\ShippingProviderKeyEnum;
use App\HttpClient\UpsHttpClient;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Throwable;

class Ups implements ShippingProviderInterface
{
    public function __construct(
        protected UpsHttpClient $httpClient,
        protected LoggerInterface $logger,
        protected SerializerInterface $serializer,
    ) {
    }

    public function supports(ShippingProviderKeyEnum $shippingProviderEnum): bool
    {
        return $shippingProviderEnum->value === ShippingProviderKeyEnum::UPS->value;
    }

    public function registerShipment(Order $order): bool
    {
        $request =UpsRegisterShippingRequest::fromOrder($order);
        try {
            $this->httpClient->registerShipping($request);
        } catch (Throwable $error) {
            $this->logger->error($error->getMessage());

            return false;
        }

        return true;
    }
}
