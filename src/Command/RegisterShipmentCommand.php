<?php

namespace Shipping\Command;

use Shipping\Entity\Order;
use Shipping\Enum\ShippingProviderKeyEnum;
use Shipping\Service\OrderServiceInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:register-shipment',
    description: 'Registers a shipment for a given shipping provider.'
)]
readonly class RegisterShipmentCommand
{
    public function __construct(
        private OrderServiceInterface $orderService
    ) {
    }

    public function __invoke(
        OutputInterface $output,
        #[Argument(description: 'Shipping provider key (ups, omniva, dhl)')]
        string $provider = ShippingProviderKeyEnum::UPS->value,
    ): int {
        $providerKey = ShippingProviderKeyEnum::tryFrom($provider);

        if ($providerKey === null) {
            $output->writeln(
                sprintf(
                    '<error>Shipment registration failed with provider %s</error>',
                    $provider
                )
            );

            return Command::FAILURE;
        }

        $order = new Order(
            id: 1,
            street: 'Blegdamsvej 9',
            postCode: '2100',
            city: 'Copenhagen',
            country: 'Denmark',
            shippingProviderKey: $providerKey,
        );

        $success = $this->orderService->registerShipping($order);

        if ($success) {
            $output->writeln(
                sprintf(
                    '<info>Shipment registered successfully with provider %s</info>',
                    $providerKey->value
                )
            );

            return Command::SUCCESS;
        }

        $output->writeln(
            sprintf(
                '<error>Shipment registration failed with provider %s</error>',
                $providerKey->value
            )
        );

        return Command::FAILURE;
    }
}