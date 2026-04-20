<?php

declare(strict_types=1);

namespace Shipping\HttpClient;

use Shipping\DTO\Request\OmnivaFindPickupPointRequest;
use Shipping\DTO\Request\OmnivaRegisterShippingRequest;
use Shipping\DTO\Response\OmnivaFindPickupPointResponse;

interface OmnivaHttpClientInterface
{
    public function findPickup(OmnivaFindPickupPointRequest $request): OmnivaFindPickupPointResponse;

    public function registerShipping(OmnivaRegisterShippingRequest $request): bool;
}
