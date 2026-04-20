<?php

declare(strict_types=1);

namespace Shipping\Tests\DataProvider\HttpClient;

final class DhlHttpClientDataProvider
{
    /**
     * @return array<string, array{int, string, string, string, string}>
     */
    public static function registerShippingProvider(): array
    {
        return [
            'copenhagen' => [1, 'Denmark', 'Main Street 1', 'Copenhagen', '2100'],
            'berlin' => [42, 'Germany', 'Unter den Linden', 'Berlin', '10117'],
            'paris' => [99, 'France', 'Rue de Rivoli', 'Paris', '75004'],
        ];
    }
}
