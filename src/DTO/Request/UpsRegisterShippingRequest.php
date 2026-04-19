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
            orderId: $order->getId(),
            country: $order->getCountry(),
            street: $order->getStreet(),
            city: $order->getCity(),
            postCode: $order->getPostCode(),
        );
    }

    public function toArray(): array
    {
        return [
            'order_id' => $this->orderId,
            'country' => $this->country,
            'street' => $this->street,
            'city' => $this->city,
            'post_code' => $this->postCode,
        ];
    }
}
