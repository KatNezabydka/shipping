<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Order as OrderEntity;

interface OrderInterface
{
    public function registerShipping(OrderEntity $order): bool;
}
