<?php

declare(strict_types=1);

namespace App\ShippingProvider\Provider;

use App\DTO\Request\OmnivaFindPickupPointRequest;
use App\DTO\Request\OmnivaRegisterShippingRequest;
use App\Entity\Order;
use App\Enum\ShippingProviderKeyEnum;
use App\HttpClient\OmnivaHttpClient;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Throwable;

class Omniva implements ShippingProviderInterface
{
    public function __construct(
        protected OmnivaHttpClient $httpClient,
        protected LoggerInterface $logger,
        protected SerializerInterface $serializer,
    ) {
    }

    public function supports(ShippingProviderKeyEnum $shippingProviderEnum): bool
    {
        return $shippingProviderEnum->value === ShippingProviderKeyEnum::OMNIVA->value;
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
                $order->getCountry(),
            );

            return $this->httpClient->registerShipping($registerShippingRequest);
        } catch (Throwable $error) {
            $this->logger->error($error->getMessage());

            return false;
        }
    }
}
