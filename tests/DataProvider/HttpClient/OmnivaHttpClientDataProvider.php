<?php

declare(strict_types=1);

namespace Shipping\Tests\DataProvider\HttpClient;

final class OmnivaHttpClientDataProvider
{
    public static function findPickupProvider(): array
    {
        return [
            'copenhagen' => ['Denmark', '2100', 42],
            'tallinn' => ['Estonia', '10001', 15],
            'riga' => ['Latvia', 'LV-1010', 8],
        ];
    }

    public static function registerShippingProvider(): array
    {
        return [
            'pickup 1' => [1, 'order-123'],
            'pickup 42' => [42, 'order-456'],
            'pickup 99' => [99, 'order-789'],
        ];
    }
}
