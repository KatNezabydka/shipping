<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\ShippingProviderKeyEnum;

readonly class Order
{
    public function __construct(
        public int $id,
        public string $street,
        public string $postCode,
        public string $city,
        public string $country,
        public ShippingProviderKeyEnum $shippingProviderKey = ShippingProviderKeyEnum::UPS,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function getPostCode(): string
    {
        return $this->postCode;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function getShippingProviderKey(): ShippingProviderKeyEnum
    {
        return $this->shippingProviderKey;
    }
}
