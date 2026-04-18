<?php

declare(strict_types=1);

namespace App\HttpClient;

use App\DTO\Request\OmnivaFindPickupPointRequest;
use App\DTO\Request\OmnivaRegisterShippingRequest;
use App\DTO\Response\OmnivaFindPickupPointResponse;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Throwable;

readonly class OmnivaHttpClient extends BaseHttpClient
{
    private const string FIND_PICK_UP_URL = 'https://omnivafake.com/pickup/find';
    private const string SHIPPING_REGISTER_URL = 'https://omnivafake.com/register';

    /**
     * @throws ExceptionInterface
     * @throws Throwable
     * @throws GuzzleException
     */
    public function findPickup(OmnivaFindPickupPointRequest $request): OmnivaFindPickupPointResponse
    {
        $json = $this->get(
            self::FIND_PICK_UP_URL,
            $this->serializer->normalize($request)
        );

        return $this->serializer->deserialize(
            $json,
            OmnivaFindPickupPointResponse::class,
            'json'
        );
    }

    /**
     * @throws Throwable
     * @throws GuzzleException
     */
    public function registerShipping(OmnivaRegisterShippingRequest $request): bool
    {
        $this->post(
            self::SHIPPING_REGISTER_URL,
            $this->serializer->normalize($request)
        );

        return true;
    }
}