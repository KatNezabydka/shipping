<?php

declare(strict_types=1);

namespace App\DTO\Request;

use App\Entity\Order;
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
            country: $order->getCountry(),
            postCode: $order->getPostCode(),
        );
    }
}
