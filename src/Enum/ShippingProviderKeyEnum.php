<?php

declare(strict_types=1);

namespace App\Enum;

enum ShippingProviderKeyEnum: string
{
    case UPS = 'ups';
    case OMNIVA = 'omniva';
    case DHL = 'dhl';
}
