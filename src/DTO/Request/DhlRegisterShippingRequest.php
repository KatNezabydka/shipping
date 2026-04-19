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
            orderId: $order->getId(),
            country: $order->getCountry(),
            address: $order->getStreet(),
            town: $order->getCity(),
            zipCode: $order->getPostCode()
        );
    }

    public function toArray(): array
    {
        return [
            'order_id' => $this->orderId,
            'country' => $this->country,
            'address'  => $this->address,
            'town' => $this->town,
            'zip_code' => $this->zipCode,
        ];
    }
}
