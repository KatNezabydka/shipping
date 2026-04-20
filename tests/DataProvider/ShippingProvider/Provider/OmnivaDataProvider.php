<?php

declare(strict_types=1);

namespace Shipping\Tests\DataProvider\ShippingProvider\Provider;

use Shipping\Enum\ShippingProviderKeyEnum;

final class OmnivaDataProvider
{
    public static function supportsProvider(): array
    {
        return [
            'omniva' => [ShippingProviderKeyEnum::OMNIVA, true],
            'ups' => [ShippingProviderKeyEnum::UPS, false],
            'dhl' => [ShippingProviderKeyEnum::DHL, false],
        ];
    }

    public static function registerShippingProvider(): array
    {
        return [
            'found pickup point' => [1, 'Denmark', 42, true, true],
            'no pickup point' => [2, 'Estonia', 0, false, false],
            'another location' => [99, 'Latvia', 15, true, true],
        ];
    }
}
