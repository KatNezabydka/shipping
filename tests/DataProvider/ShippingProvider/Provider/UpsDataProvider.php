<?php

declare(strict_types=1);

namespace Shipping\Tests\DataProvider\ShippingProvider\Provider;

use Shipping\Enum\ShippingProviderKeyEnum;

final class UpsDataProvider
{
    /**
     * @return array<string, array{ShippingProviderKeyEnum, bool}>
     */
    public static function supportsProvider(): array
    {
        return [
            'ups' => [ShippingProviderKeyEnum::UPS, true],
            'dhl' => [ShippingProviderKeyEnum::DHL, false],
            'omniva' => [ShippingProviderKeyEnum::OMNIVA, false],
        ];
    }

    /**
     * @return array<string, array{int, string, bool}>
     */
    public static function registerShippingProvider(): array
    {
        return [
            'success 1' => [1, 'Main Street 1', true],
            'success 42' => [42, 'Oak Avenue 5', true],
            'failure' => [99, 'Pine Road 3', false],
        ];
    }
}
