<?php

declare(strict_types=1);

namespace Shipping\Tests\Unit\ShippingProvider\Provider;

use Shipping\DTO\Request\OmnivaFindPickupPointRequest;
use Shipping\DTO\Request\OmnivaRegisterShippingRequest;
use Shipping\DTO\Response\OmnivaFindPickupPointResponse;
use Shipping\Entity\Order as OrderEntity;
use Shipping\Enum\ShippingProviderKeyEnum;
use Shipping\HttpClient\OmnivaHttpClient;
use Shipping\ShippingProvider\Provider\Omniva;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Throwable;

class OmnivaTest extends TestCase
{
    private OmnivaHttpClient&MockObject $httpClientMock;
    private LoggerInterface&MockObject $loggerMock;
    private Omniva $omnivaProvider;

    protected function setUp(): void
    {
        $this->httpClientMock = $this->createMock(OmnivaHttpClient::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->omnivaProvider = new Omniva($this->httpClientMock, $this->loggerMock);
    }

    public function testSupportsReturnsTrueForOmniva(): void
    {
        $this->assertTrue($this->omnivaProvider->supports(ShippingProviderKeyEnum::OMNIVA));
    }

    public function testSupportsReturnsFalseForOther(): void
    {
        $this->assertFalse($this->omnivaProvider->supports(ShippingProviderKeyEnum::UPS));
    }

    public function testRegisterShipmentReturnsTrueWhenPickupPointFound(): void
    {
        $order = new OrderEntity(
            id: 1,
            street: 'Street 1',
            postCode: '1000',
            city: 'City',
            country: 'Denmark',
            shippingProviderKey: ShippingProviderKeyEnum::OMNIVA
        );

        $findRequest = OmnivaFindPickupPointRequest::fromOrder($order);
        $pickupResponse = OmnivaFindPickupPointResponse::fromResponse(['pickupPoint' => 42]);
        $registerRequest = new OmnivaRegisterShippingRequest(42, 'Denmark');

        $this->httpClientMock->expects($this->once())
            ->method('findPickup')
            ->with($this->callback(fn(OmnivaFindPickupPointRequest $r) => $r->toArray() === $findRequest->toArray()))
            ->willReturn($pickupResponse);

        $this->httpClientMock->expects($this->once())
            ->method('registerShipping')
            ->with($this->callback(fn(OmnivaRegisterShippingRequest $r) => $r->toArray() === $registerRequest->toArray()))
            ->willReturn(true);

        $result = $this->omnivaProvider->registerShipment($order);

        $this->assertTrue($result);
    }

    public function testRegisterShipmentReturnsFalseWhenNoPickupPoint(): void
    {
        $order = new OrderEntity(
            id: 2,
            street: 'Street 2',
            postCode: '2000',
            city: 'City2',
            country: 'Denmark',
            shippingProviderKey: ShippingProviderKeyEnum::OMNIVA
        );

        $pickupResponse = OmnivaFindPickupPointResponse::fromResponse(['pickupPoint' => 0]);
        $findRequest = OmnivaFindPickupPointRequest::fromOrder($order);

        $this->httpClientMock->expects($this->once())
            ->method('findPickup')
            ->with($this->callback(fn(OmnivaFindPickupPointRequest $r) => $r->toArray() === $findRequest->toArray()))
            ->willReturn($pickupResponse);

        $this->loggerMock->expects($this->once())
            ->method('error')
            ->with('Pickup point not found for Omniva');

        $result = $this->omnivaProvider->registerShipment($order);

        $this->assertFalse($result);
    }

    public function testRegisterShipmentReturnsFalseOnException(): void
    {
        $order = new OrderEntity(
            id: 3,
            street: 'Street 3',
            postCode: '3000',
            city: 'City3',
            country: 'Denmark',
            shippingProviderKey: ShippingProviderKeyEnum::OMNIVA
        );

        $findRequest = OmnivaFindPickupPointRequest::fromOrder($order);

        $this->httpClientMock->expects($this->once())
            ->method('findPickup')
            ->with($this->callback(fn(OmnivaFindPickupPointRequest $r) => $r->toArray() === $findRequest->toArray()))
            ->willThrowException(new RuntimeException('Network error'));

        $this->loggerMock->expects($this->once())
            ->method('error')
            ->with($this->isInstanceOf(Throwable::class));

        $result = $this->omnivaProvider->registerShipment($order);

        $this->assertFalse($result);
    }
}