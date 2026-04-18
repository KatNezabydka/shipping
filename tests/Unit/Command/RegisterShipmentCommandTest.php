<?php

declare(strict_types=1);

namespace App\Tests\Unit\Command;

use App\Command\RegisterShipmentCommand;
use App\Entity\Order;
use App\Enum\ShippingProviderKeyEnum;
use App\Service\OrderInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class RegisterShipmentCommandTest extends TestCase
{
    private OrderInterface&MockObject $orderServiceMock;

    private CommandTester $commandTester;

    protected function setUp(): void
    {
        $this->orderServiceMock = $this->createMock(OrderInterface::class);

        $command = new RegisterShipmentCommand($this->orderServiceMock);

        $application = new Application();
        $application->add($command);

        $this->commandTester = new CommandTester($application->find('app:register-shipment'));
    }

    public function testSuccessfulRegistration(): void
    {
        $this->orderServiceMock
            ->expects($this->once())
            ->method('registerShipping')
            ->willReturn(true);

        $this->commandTester->execute([
            'provider' => 'ups',
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Shipment registered was successfully with provider ups', $output);
        $this->assertSame(0, $this->commandTester->getStatusCode());
    }

    public function testFailedRegistration(): void
    {
        $this->orderServiceMock
            ->expects($this->once())
            ->method('registerShipping')
            ->willReturn(false);

        $this->commandTester->execute([
            'provider' => 'dhl',
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Shipment registered was unsuccessfully with provider dhl', $output);
        $this->assertSame(1, $this->commandTester->getStatusCode());
    }

    public function testDefaultProviderWhenInvalidPassed(): void
    {
        $this->orderServiceMock
            ->expects($this->once())
            ->method('registerShipping')
            ->with($this->callback(fn(Order $order) => $order->getShippingProviderKey() === ShippingProviderKeyEnum::UPS))
            ->willReturn(true);

        $this->commandTester->execute([
            'provider' => 'invalid-provider',
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Shipment registered was successfully with provider ups', $output);
        $this->assertSame(0, $this->commandTester->getStatusCode());
    }
}