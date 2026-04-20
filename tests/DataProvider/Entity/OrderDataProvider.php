<?php

declare(strict_types=1);

namespace Shipping\Tests\DataProvider\Entity;

use Shipping\Enum\ShippingProviderKeyEnum;

final class OrderDataProvider
{
    public static function orderProvider(): array
    {
        return [
            'ups' => [42, 'Main Street 1', '2100', 'Copenhagen', 'Denmark', ShippingProviderKeyEnum::UPS],
            'dhl' => [99, 'Oak Avenue 5', '8000', 'Aarhus', 'Denmark', ShippingProviderKeyEnum::DHL],
            'omniva' => [12, 'Pine Road 3', '10001', 'Tallinn', 'Estonia', ShippingProviderKeyEnum::OMNIVA],
        ];
    }
}
