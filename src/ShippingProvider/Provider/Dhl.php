<?php

declare(strict_types=1);

namespace App\ShippingProvider\Provider;

use App\DTO\Request\DhlRegisterShippingRequest;
use App\Entity\Order;
use App\Enum\ShippingProviderKeyEnum;
use App\HttpClient\DhlHttpClient;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Throwable;

class Dhl implements ShippingProviderInterface
{
    public function __construct(
        protected DhlHttpClient $httpClient,
        protected LoggerInterface $logger,
        protected SerializerInterface $serializer,
    ) {
    }

    public function supports(ShippingProviderKeyEnum $shippingProviderEnum): bool
    {
        return $shippingProviderEnum->value === ShippingProviderKeyEnum::DHL->value;
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
