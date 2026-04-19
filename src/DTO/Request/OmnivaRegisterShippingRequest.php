<?php

declare(strict_types=1);

namespace Shipping\DTO\Request;
use Symfony\Component\Serializer\Attribute\SerializedName;

readonly class OmnivaRegisterShippingRequest
{
    public function __construct(
        #[SerializedName('pickup_point_id')]
        public int $pickupPointId,
        #[SerializedName('order_id')]
        public string $orderId,
    ) {
    }

    public function toArray(): array
    {
        return [
            'pickup_point_id' => $this->pickupPointId,
            'order_id' => $this->orderId,
        ];
    }
}
