<?php

declare(strict_types=1);

namespace App\Tests\Unit\ShippingProvider\Provider;

use App\DTO\Request\UpsRegisterShippingRequest;
use App\Entity\Order as OrderEntity;
use App\Enum\ShippingProviderKeyEnum;
use App\ShippingProvider\Provider\Ups;
use App\HttpClient\UpsHttpClientInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Throwable;

class UpsTest extends TestCase
{
    private UpsHttpClientInterface&MockObject $httpClientMock;
    private LoggerInterface&MockObject $loggerMock;
    private Ups $upsProvider;

    protected function setUp(): void
    {
        $this->httpClientMock = $this->createMock(UpsHttpClientInterface::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->upsProvider = new Ups($this->httpClientMock, $this->loggerMock);
    }

    public function testSupportsReturnsTrueForUps(): void
    {
        $this->assertTrue($this->upsProvider->supports(ShippingProviderKeyEnum::UPS));
    }

    public function testSupportsReturnsFalseForOther(): void
    {
        $this->assertFalse($this->upsProvider->supports(ShippingProviderKeyEnum::DHL));
    }

    public function testRegisterShipmentCallsHttpClientAndReturnsTrue(): void
    {
        $order = new OrderEntity(
            id: 1,
            street: 'Street 1',
            postCode: '1000',
            city: 'City',
            country: 'Denmark',
            shippingProviderKey: ShippingProviderKeyEnum::UPS
        );

        $requestDto = UpsRegisterShippingRequest::fromOrder($order);

        $this->httpClientMock->expects($this->once())
            ->method('registerShippingRequest')
            ->with($this->callback(fn(UpsRegisterShippingRequest $r) => $r->toArray() === $requestDto->toArray()));

        $result = $this->upsProvider->registerShipment($order);

        $this->assertTrue($result);
    }

    public function testRegisterShipmentReturnsFalseOnException(): void
    {
        $order = new OrderEntity(
            id: 2,
            street: 'Street 2',
            postCode: '2000',
            city: 'City2',
            country: 'Denmark',
            shippingProviderKey: ShippingProviderKeyEnum::UPS
        );

        $requestDto = UpsRegisterShippingRequest::fromOrder($order);

        $this->httpClientMock->expects($this->once())
            ->method('registerShippingRequest')
            ->with($this->callback(fn(UpsRegisterShippingRequest $r) => $r->toArray() === $requestDto->toArray()))
            ->willThrowException(new RuntimeException('Network error'));

        $this->loggerMock->expects($this->once())
            ->method('error')
            ->with($this->isInstanceOf(Throwable::class));

        $result = $this->upsProvider->registerShipment($order);

        $this->assertFalse($result);
    }
}