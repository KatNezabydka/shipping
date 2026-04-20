<?php

declare(strict_types=1);

namespace Shipping\Tests\DataProvider\ShippingProvider\Provider;

use Shipping\Enum\ShippingProviderKeyEnum;

final class DhlDataProvider
{
    /**
     * @return array<string, array{ShippingProviderKeyEnum, bool}>
     */
    public static function supportsProvider(): array
    {
        return [
            'dhl' => [ShippingProviderKeyEnum::DHL, true],
            'ups' => [ShippingProviderKeyEnum::UPS, false],
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
