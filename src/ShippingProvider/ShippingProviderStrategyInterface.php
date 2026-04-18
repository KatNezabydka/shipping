<?php

declare(strict_types=1);

namespace App\ShippingProvider;

use App\Enum\ShippingProviderKeyEnum;
use App\ShippingProvider\Provider\ShippingProviderInterface;

interface ShippingProviderStrategyInterface
{
    public function getProvider(ShippingProviderKeyEnum $shippingProviderEnum): ShippingProviderInterface;
}
