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
}
