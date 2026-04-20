<?php

declare(strict_types=1);

namespace Shipping\DTO\Request;

use Shipping\Entity\Order;
use Symfony\Component\Serializer\Attribute\SerializedName;

readonly class UpsRegisterShippingRequest
{
    public function __construct(
        #[SerializedName('order_id')]
        public int $orderId,
        public string $country,
        public string $street,
        public string $city,
        #[SerializedName('post_code')]
        public string $postCode,
    ) {
    }

    public static function fromOrder(Order $order): self
    {
        return new self(
            orderId: $order->id,
            country: $order->country,
            street: $order->street,
            city: $order->city,
            postCode: $order->postCode,
        );
    }
}
