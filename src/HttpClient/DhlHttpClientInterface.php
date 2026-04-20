<?php

declare(strict_types=1);

namespace Shipping\HttpClient;

use Shipping\DTO\Request\DhlRegisterShippingRequest;

interface DhlHttpClientInterface
{
    public function registerShipping(DhlRegisterShippingRequest $request): bool;
}
