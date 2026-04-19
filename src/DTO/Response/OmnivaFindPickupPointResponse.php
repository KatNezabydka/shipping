<?php

declare(strict_types=1);

namespace Shipping\DTO\Response;

readonly class OmnivaFindPickupPointResponse
{
    public function __construct(
        public int $pickupPoint,
    ) {
    }

    public static function fromResponse(array $response): self
    {
        return new self(
            pickupPoint: $response['pickupPoint'],
        );
    }

    public function hasPickupPoint(): bool
    {
        return !empty($this->pickupPoint);
    }
}
