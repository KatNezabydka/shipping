<?php

declare(strict_types=1);

namespace Shipping\Tests\DataProvider\Service;

use Shipping\Enum\ShippingProviderKeyEnum;

final class OrderDataProvider
{
    /**
     * @return array<string, array{ShippingProviderKeyEnum, bool}>
     */
    public static function registerShippingProvider(): array
    {
        return [
            'ups succeeds' => [ShippingProviderKeyEnum::UPS, true],
            'dhl fails' => [ShippingProviderKeyEnum::DHL, false],
            'omniva succeeds' => [ShippingProviderKeyEnum::OMNIVA, true],
        ];
    }
}
