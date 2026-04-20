<?php

declare(strict_types=1);

namespace Shipping\DTO\Request;

use Shipping\Entity\Order;
use Symfony\Component\Serializer\Attribute\SerializedName;

readonly class OmnivaFindPickupPointRequest
{
    public function __construct(
        public string $country,
        #[SerializedName('post_code')]
        public string $postCode,
    ) {
    }

    public static function fromOrder(Order $order): self
    {
        return new self(
            country: $order->country,
            postCode: $order->postCode,
        );
    }
}
