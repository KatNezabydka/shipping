<?php

declare(strict_types=1);

namespace Shipping\Tests\Unit\Command;

use Shipping\Service\OrderServiceInterface;
use Shipping\Tests\DataProvider\Command\RegisterShipmentCommandDataProvider;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\ApplicationTester;
use PHPUnit\Framework\Attributes\DataProviderExternal;

class RegisterShipmentCommandTest extends KernelTestCase
{
    private Application $application;
    private OrderServiceInterface $orderServiceMock;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->orderServiceMock = $this->createMock(OrderServiceInterface::class);
        static::getContainer()->set(OrderServiceInterface::class, $this->orderServiceMock);

        $this->application = new Application(self::$kernel);
        $this->application->setAutoExit(false);
    }

    #[DataProviderExternal(RegisterShipmentCommandDataProvider::class, 'executeCommandProvider')]
    public function testExecuteCommand(
        string $provider,
        bool $serviceReturnsSuccess,
        int $expectedStatusCode,
        string $expectedOutput,
        int|null $expectedCallCount,
    ): void {
        if ($expectedCallCount !== null) {
            $this->orderServiceMock->expects($this->exactly($expectedCallCount))
                ->method('registerShipping')
                ->willReturn($serviceReturnsSuccess);
        } else {
            $this->orderServiceMock->expects($this->never())
                ->method('registerShipping');
        }

        $tester = new ApplicationTester($this->application);
        $tester->run(['command' => 'app:register-shipment', 'provider' => $provider]);

        $this->assertSame($expectedStatusCode, $tester->getStatusCode());
        $this->assertStringContainsString($expectedOutput, $tester->getDisplay());
    }
}
