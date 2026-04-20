<?php

declare(strict_types=1);

namespace Shipping\Service;

use Shipping\Entity\Order;

interface OrderServiceInterface
{
    public function registerShipping(Order $order): bool;
}
