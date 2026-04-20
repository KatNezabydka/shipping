<?php

declare(strict_types=1);

namespace Shipping\Tests\DataProvider\HttpClient;

final class UpsHttpClientDataProvider
{
    /**
     * @return array<string, array{int, string, string, string, string}>
     */
    public static function registerShippingProvider(): array
    {
        return [
            'copenhagen' => [1, 'Denmark', 'Blegdamsvej 9', 'Copenhagen', '2100'],
            'aarhus' => [42, 'Denmark', 'Thorvaldsensgade 12', 'Aarhus', '8000'],
            'odense' => [99, 'Denmark', 'Nørregade 5', 'Odense', '5000'],
        ];
    }
}
