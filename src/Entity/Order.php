<?php

declare(strict_types=1);

namespace Shipping\Entity;

use Shipping\Enum\ShippingProviderKeyEnum;

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
}
