<?php

declare(strict_types=1);

namespace Shipping\DTO\Request;

use Shipping\Entity\Order;
use Symfony\Component\Serializer\Attribute\SerializedName;

readonly class DhlRegisterShippingRequest
{
    public function __construct(
        #[SerializedName('order_id')]
        public int $orderId,
        public string $country,
        #[SerializedName('address')]
        public string $address,
        #[SerializedName('town')]
        public string $town,
        #[SerializedName('zip_code')]
        public string $zipCode,
    ) {}

    public static function fromOrder(Order $order): self
    {
        return new self(
            orderId: $order->id,
            country: $order->country,
            address: $order->street,
            town: $order->city,
            zipCode: $order->postCode
        );
    }
}
