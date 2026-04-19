<?php

declare(strict_types=1);

namespace Shipping\ShippingProvider;

use Shipping\Enum\ShippingProviderKeyEnum;
use Shipping\ShippingProvider\Provider\ShippingProviderInterface;
use InvalidArgumentException;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

readonly class ShippingProviderStrategy implements ShippingProviderStrategyInterface
{
    /** @param iterable<ShippingProviderInterface> $providers */
    public function __construct(
        #[AutowireIterator(ShippingProviderInterface::TAG)]
        private iterable $providers
    ) {
    }

    public function getProvider(ShippingProviderKeyEnum $shippingProviderEnum): ShippingProviderInterface
    {
        foreach ($this->providers as $provider) {
            if ($provider->supports($shippingProviderEnum)) {
                return $provider;
            }
        }

        throw new InvalidArgumentException(
            sprintf(
                'No provider found for type: %s',
                $shippingProviderEnum->value
            )
        );
    }
}
