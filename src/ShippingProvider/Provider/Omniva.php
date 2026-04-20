<?php

declare(strict_types=1);

namespace Shipping\ShippingProvider\Provider;

use Psr\Log\LoggerInterface;
use Shipping\DTO\Request\OmnivaFindPickupPointRequest;
use Shipping\DTO\Request\OmnivaRegisterShippingRequest;
use Shipping\Entity\Order;
use Shipping\Enum\ShippingProviderKeyEnum;
use Shipping\HttpClient\OmnivaHttpClientInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Throwable;

class Omniva implements ShippingProviderInterface
{
    public function __construct(
        protected OmnivaHttpClientInterface $httpClient,
        protected LoggerInterface $logger,
        protected SerializerInterface $serializer,
    ) {
    }

    public function supports(ShippingProviderKeyEnum $shippingProviderEnum): bool
    {
        return ShippingProviderKeyEnum::OMNIVA === $shippingProviderEnum;
    }

    /**
     * @throws Throwable
     */
    public function registerShipment(Order $order): bool
    {
        try {
            $request = OmnivaFindPickupPointRequest::fromOrder($order);
            $pickupPointResponse = $this->httpClient->findPickup($request);

            if (!$pickupPointResponse->hasPickupPoint()) {
                $this->logger->error('Pickup point not found for Omniva');

                return false;
            }

            $registerShippingRequest = new OmnivaRegisterShippingRequest(
                $pickupPointResponse->pickupPoint,
                $order->country,
            );

            return $this->httpClient->registerShipping($registerShippingRequest);
        } catch (Throwable $error) {
            $this->logger->error($error->getMessage());

            return false;
        }
    }
}
