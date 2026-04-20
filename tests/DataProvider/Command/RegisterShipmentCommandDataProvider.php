<?php

declare(strict_types=1);

namespace Shipping\Tests\DataProvider\Command;

final class RegisterShipmentCommandDataProvider
{
    public static function executeCommandProvider(): array
    {
        return [
            'successful ups registration' => ['ups', true, 0, 'Shipment registered successfully with provider ups', 1],
            'failed dhl registration' => ['dhl', false, 1, 'Shipment registration failed with provider dhl', 1],
            'failed omniva registration' => ['omniva', false, 1, 'Shipment registration failed with provider omniva', 1],
            'invalid provider' => ['invalid-provider', false, 1, 'Shipment registration failed with provider invalid-provider', null],
        ];
    }
}
