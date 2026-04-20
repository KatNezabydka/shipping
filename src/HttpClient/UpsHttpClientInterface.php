<?php

declare(strict_types=1);

namespace Shipping\HttpClient;

use Shipping\DTO\Request\UpsRegisterShippingRequest;

interface UpsHttpClientInterface
{
    public function registerShipping(UpsRegisterShippingRequest $request): bool;
}
